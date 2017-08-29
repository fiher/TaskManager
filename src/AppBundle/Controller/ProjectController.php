<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comments;
use AppBundle\Entity\Files;
use AppBundle\Entity\User;
use AppBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Project controller.
 *
 * @Route("project")
 */
class ProjectController extends Controller
{
    /**
     * Lists all project entities.
     *
     * @Route("/archive", name="project_archive")
     * @Method("GET")
     */
    public function archiveAction()
    {
        //this function returns "" if the user is allowed and if not returns $this->render
        $forbidden = $this->checkCredentials("all");
        if($forbidden){
            return $forbidden;
        }
        $projectService = $this->get('app.service.projects_service');
        $user = $this->getUser();
        $projects = $projectService->getArchivedProjects($user);
        usort($projects, array($this, "sortProjects"));
        return $this->render('project/index.html.twig', array(
            'projects' => $projects,
        ));
    }
    /**
     *
     * @Route("/designer/{username}", name="project_designer")
     * @Method("GET")
     */
    public function showDesignerOnlyProjects(string $username)
    {
        //this function returns "" if the user is allowed and if not returns $this->render
        $forbidden = $this->checkCredentials("all");
        if($forbidden){
            return $forbidden;
        }
        $projectService = $this->get('app.service.projects_service');
        $userService = $this->get('app.service.users_service');
        /** @var User $user */
        $user = $userService->getUserByUsername($username);
        $projects = array_reverse($projectService->getDesignerProjects($user->getFullName()));
        $projects = $projectService->addCommentsToProjects($projects, $user);
        $projects = $projectService->filterProjects($projects, $user);
        $addFilesForm = $this->createForm('AppBundle\Form\AddFilesType');
        return $this->render('project/index.html.twig', array(
            'projects' => $projects,
            'add_files_form'=> $addFilesForm
        ));
    }
    /**
     * This function shows only Executioner projects. Seen only from LittleBoss
     *
     * @Route("/executioner", name="project_executioner")
     * @Method("GET")
     */
    public function showExecutionerOnlyProjects()
    {
        //this function returns "" if the user is allowed and if not returns $this->render
        $forbidden = $this->checkCredentials("all");
        if($forbidden){
            return $forbidden;
        }
        $projectService = $this->get('app.service.projects_service');
        /** @var User $user */
        $user = $this->getUser();
        $projects = array_reverse($projectService->getExecutionerProjects());
        $filteredProjects = $projectService->filterProjects($projects,$user);
        $filteredProjects = $projectService->addCommentsToProjects($filteredProjects,$user);
        $addFilesForm = $this->createForm('AppBundle\Form\AddFilesType');
        return $this->render('project/index.html.twig', array(
            'projects' => $filteredProjects,
            'add_files_form'=> $addFilesForm
        ));
    }
    /**
     * Lists all project entities.
     *
     * @Route("/", name="project_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        //this function returns "" if the user is allowed and if not returns $this->render
        $forbidden = $this->checkCredentials("all");
        if($forbidden){
            return $forbidden;
        }
        $projectService = $this->get('app.service.projects_service');
        /** @var User $user */
        $user = $this->getUser();
        dump($user->getFullName());
        $projects = array_reverse($projectService->getProjects($user));
        dump($projects);
        $filteredProjects = $projectService->filterProjects($projects,$user);
        $filteredProjects = $projectService->addCommentsToProjects($filteredProjects,$user);
        $addFilesForm = $this->createForm('AppBundle\Form\AddFilesType');
        return $this->render('project/index.html.twig', array(
            'projects' => $filteredProjects,
            'add_files_form'=> $addFilesForm
        ));
    }
    /**
     * Creates a new project entity.
     *
     * @Route("/new", name="project_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        //this function returns "" if the user is allowed and if not returns $this->render
        $forbidden = $this->checkCredentials(array("Manager","LittleBoss","Boss"));
        if($forbidden){
            return $forbidden;
        }
        /** @var  $user User */
        $user = $this->getUser();
        $project = new Project();
        $projectService = $this->get('app.service.projects_service');
        $form = $this->createForm('AppBundle\Form\ProjectType', $project);
        if($user->getType() != "LittleBoss") {
            $form = $projectService->removeFormFieldsForManagers($form);
            if($user->getUsername() == 'winbet.online') {
                $form = $projectService->addDesignerFieldForManagers($form,$project->getDesigner());
            }
        }else{
            $form = $projectService->addSecondDesignerField($form,$project->getDesigner());
        }
        /** @var Form $form */
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $managerFiles = $managerFiles = $request->files->get('appbundle_project')['managerFiles'];
            $projectService->createProject($project, $user);
            if($managerFiles){
                $filesService = $this->get('app.service.files_service');
                foreach ($managerFiles as $managerFile) {
                    /** @var UploadedFile  $managerFile */
                    if($managerFile) {
                        $fileName = $filesService->uploadFileAndReturnName($managerFile, $this->getParameter('files_directory'));
                        $filesService->createFile($fileName, $project, $user, $managerFile->getExtension());
                    }
                }
            }
            return $this->redirectToRoute('project_show', array('id' => $project->getId()));
        }
        return $this->render('project/new.html.twig', array(
            'project' => $project,
            'form' => $form->createView(),
        ));
    }
    /**
     * Finds and displays a project entity.
     *
     * @Route("/{id}", name="project_show")
     * @Method({"GET", "POST"})
     */
    public function showAction(Project $project)
    {
        //this function returns "" if the user is allowed and if not returns $this->render
        $forbidden = $this->checkCredentials("all");
        if($forbidden){
            return $forbidden;
        }
        /**
         * @var $user User
         */
        $projectService = $this->get('app.service.projects_service');
        $commentsService = $this->get('app.service.comments_service');
        $user = $this->getUser();
        $deleteForm = $this->createDeleteForm($project);
        $comments = $commentsService->findCommentsByProjectID($project->getId());
        $comments = $commentsService->filterComments($comments,$user);
        $project =  $projectService->setProject($project, $user);
        $projectService->flushProject($project);
        $comment = new Comments();
        $addFilesForm = $this->createForm('AppBundle\Form\AddFilesType');
        $form = $this->createForm('AppBundle\Form\CommentsType', $comment);
        if ($user->getType() != "LittleBoss") {
            $form->remove("toUser");
        }
        return $this->render('project/show.html.twig', array(
            'project' => $project,
            'delete_form' => $deleteForm->createView(),
            'comment' => $comment,
            'comments' => $comments,
            'form' => $form->createView(),
            'add_files_form'=> $addFilesForm->createView(),
            'designerFiles' => $project->getDesignerFiles(),
            'managerFiles' => $project->getManagerFiles(),
            'littleBossFiles' => $project->getLittleBossFiles()
        ));
    }
    /**
     * Displays a form to edit an existing project entity.
     *
     * @Route("/{id}/edit", name="project_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Project $project)
    {
        //this function returns "" if the user is allowed and if not returns $this->render
        $forbidden = $this->checkCredentials(array("Manager","LittleBoss","Boss"));
        if($forbidden) {
            return $forbidden;
        }
        $projectService = $this->get('app.service.projects_service');
        $userType = $this->getUser()->getType();
        $deleteForm = $this->createDeleteForm($project);
        $project->setHold(false);
        $editForm = $this->createForm('AppBundle\Form\ProjectType', $project);
        $editForm->remove('managerFiles');
        $editForm->remove('term');
        $editForm->add('term',DateType::class, array(
            'widget' => 'choice',
            'label'=>"Краен срок",
            'data'=>$project->getTerm()
        ));
        if ($userType != "LittleBoss") {
            $editForm = $projectService->removeFormFieldsForManagers($editForm);
        }
        if ($userType == "Designer") {
            $editForm = $projectService->removeFormFieldsForDesigners($editForm);
        }
        if ($userType == "LittleBoss") {
            $editForm = $projectService->addSecondDesignerField($editForm,$project->getSecondDesigner());
        }
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->set('success', "Успешно променихте заявката!");
            return $this->redirectToRoute('project_show', array('id' => $project->getId()));
        }
        return $this->render('project/edit.html.twig', array(
            'project' => $project,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a project entity.
     *
     * @Route("/{id}", name="project_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Project $project)
    {
        //this function returns "" if the user is allowed and if not returns $this->render
        $forbidden = $this->checkCredentials(array("LittleBoss","Boss"));
        if ($forbidden) {
            return $forbidden;
        }
        $projectService = $this->get('app.service.projects_service');
        $form = $this->createDeleteForm($project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $projectService->deleteProject($project);
        }
        return $this->redirectToRoute('project_index');
    }
    /**
     * Creates a form to delete a project entity.
     *
     * @param Project $project The project entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Project $project)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('project_delete', array('id' => $project->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    /**
     *Updates project by one property only.
     *
     * @Route("/{id}/update", name="project_update")
     * @Route("/{id}/fastupdate", name="project_fast_update")
     * @Method("POST")
     */
    public function updateAction(Request $request, Project $project)
    {
        //this function returns "" if the user is allowed and if not returns $this->render
        $forbidden = $this->checkCredentials("all");
        if ($forbidden) {
            return $forbidden;
        }
        $referer = $request->headers->get('referer');
        $projectService = $this->get('app.service.projects_service');
        $fileService = $this->get('app.service.files_service');
        /** @var User $user */
        $user = $this->getUser();
        if (isset($_POST['approve'])) {
           $project = $projectService->approveProject($project);
            $this->successMessage("Успешно одобрихте заявката!");
        } elseif (isset($_POST['reject'])) {
            $project = $projectService->rejectProject($project, $user);
            $this->successMessage("Успешно отхвърлихте заявката!");
        } elseif (isset($_POST['archive'])) {
            $project = $projectService->archiveProject($project);
            $this->successMessage("Успешно архивирахте заявката!");
        }elseif (isset($_POST['hold'])) {
            $project = $projectService->setProjectOnHold($project);
            $this->successMessage("Заявката успешно сложена на изчакване!");
        }elseif (isset($_POST['forApproval'])) {
            $project = $projectService->setProjectForApproval($project);
            $this->successMessage("Заявката успешно сложена за одобрение!");
        }elseif (isset($_POST['working'])) {
            $project = $projectService->setProjectWorking($project, $user);
        }elseif (isset($_POST['link'])) {
            $project->setDesignerLink($_POST['link']);
        }
        elseif (isset($_POST['rejectFile'])) {
           $fileService->rejectFile($_POST['rejectFile']);
        }
        $projectService->updateProject($project);
        return $this->redirect($referer);
    }
    private function checkCredentials($allowedUserRoles)
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $lastUsername = $authenticationUtils->getLastUsername();
        /** @var User $user */
        $user = $this->getUser();
        if ($user) {
            if ($allowedUserRoles == "all") {
                return "";
            }
            foreach (explode(" ",$user->getRole()) as $role) {
                if (in_array($role,$allowedUserRoles)) {
                    return "";
                }
            }
            $this->errorMessage("Нямате достъп до тази страница!");
            return  $this->render('::base.html.twig');
        }
        $this->errorMessage("Моля, първо влезте в профила си!");
        return  $this->render('@App/Security/login.html.twig',array(
            'last_username'=> $lastUsername
        ));
    }
    /**
     *Uploads images
     *
     * @Route("/{id}/fileUpload", name="file_upload")
     * @Method("POST")
     */
    public function uploadFile(Request $request, Project $project)
    {
        //this function returns "" if the user is allowed and if not returns $this->render
        $forbidden = $this->checkCredentials(array("Designer","LittleBoss","Manager","Executioner"));
        if ($forbidden) {
            return $forbidden;
        }
        $referer = $request->headers->get('referer');
        $user = $this->getUser();
        $files = $managerFiles = $request->files->get('appbundle_file')['files'];
        $filesService = $this->get('app.service.files_service');
        foreach ($files as $file) {
            /** @var UploadedFile $file */
            $fileName = $filesService->uploadFileAndReturnName($file,$this->getParameter('files_directory'));
            $filesService->createFile($fileName, $project, $user,$file->getExtension());
        }
        $this->successMessage('Файловете успешно качени!');
        return $this->redirect($referer);
    }
    /**
     *Uploads images
     *
     * @Route("/{id}/fileDelete", name="file_delete")
     * @Method("POST")
     */
    public function deleteFile(Request $request, Files $file)
    {
        //this function returns "" if the user is allowed and if not returns $this->render
        $forbidden = $this->checkCredentials(array("Manager","LittleBoss","Boss"));
        if ($forbidden) {
            return $forbidden;
        }
        $referer = $request->headers->get('referer');
        $filesService = $this->get('app.service.files_service');
        $filesService->deleteFile($file);
        return $this->redirect($referer);
    }
    public function sortProjects(Project $a,Project $b)
    {
        $projectAOver = strtotime($a->getOverDate()->format("Y-m-d"));
        $projectBOver = strtotime($b->getOverDate()->format("Y-m-d"));
        return $projectBOver - $projectAOver;
    }
    private function successMessage (string $message)
    {
        $this->get('session')->getFlashBag()->set('success', $message);
    }
    private function errorMessage (string $message)
    {
        $this->get('session')->getFlashBag()->set('error', $message);
    }
}

<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Comments;
use AppBundle\Entity\Files;
use AppBundle\Entity\User;
use AppBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Project controller.
 *
 * @Route("project")
 */
class ProjectController extends Controller
{
    //if new term is entered without a term date this will be set as a date then it will be checked when rendering them
    //if term is this exact date and if so - we display "No date" in bulgarian!
    const NO_TERM_DEFAULT_VALUE = '2050-03-19';
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
        /** @var User $user */
        $user = $this->getUser();

        $userType = $user->getType();
        $em = $this->getDoctrine()->getManager();
        $projects = $em->getRepository('AppBundle:Project')->findArchivedProjects();

        $filteredProjects = [];
        foreach ($projects as $project){
            /** @var $project Project */
            $project->setClass("archive");
            $project->setStatus("Приключено");
            if($user->getType() == "LittleBoss"){
                $filteredProjects[] = $project;
            }elseif($project->getDesigner() == $user->getFullName()
                || $project->getExecutioner() == $user->getFullName()
                || $project->getFromUser() == $user->getFullName()){
                $filteredProjects[] = $project;
            }

        }
        usort($filteredProjects, array($this, "sortProjects"));
        return $this->render('project/index.html.twig', array(
            'projects' => $filteredProjects,
        ));
    }

    /**
     *
     * @Route("/designer/{username}", name="project_designer")
     * @Method("GET")
     */
    public function showDesignerOnlyProjects(Request $request, string $username){
        //this function returns "" if the user is allowed and if not returns $this->render
        $forbidden = $this->checkCredentials("all");
        if($forbidden){
            return $forbidden;
        }
        $commentsService = $this->get('app.service.comments_service');
        $projectService = $this->get('app.service.projects_service');
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('AppBundle:User');
        /** @var User $user */
        $user = $query->loadUserByUsername($username);
        $em = $this->getDoctrine()->getManager();
        $projects = $em->getRepository('AppBundle:Project')->findAll();
        $projects = array_reverse($projects);
        $filteredProjects = $projectService->filterProjects($projects,$user,"Designer");
        foreach ($filteredProjects as $project){
            /** @var Project $project */
            $comments = $this->getDoctrine()
                ->getRepository('AppBundle:Comments')
                ->findByProjectID($project->getId());
            $project->setComments($commentsService->filterComments($comments,$user));
        }
        $addFilesForm = $this->createForm('AppBundle\Form\AddFilesType');
        return $this->render('project/index.html.twig', array(
            'projects' => $filteredProjects,
            'add_files_form'=> $addFilesForm
        ));
    }	
    /**
     * This function shows only Executioner projects. Seen only from LittleBoss
     *
     * @Route("/executioner", name="project_executioner")
     * @Method("GET")
     */
    public function showExecutionerOnlyProjects(){
        //this function returns "" if the user is allowed and if not returns $this->render
    
        //this function returns "" if the user is allowed and if not returns $this->render
        $forbidden = $this->checkCredentials("all");
        if($forbidden){
            return $forbidden;
        }
        $commentsService = $this->get('app.service.comments_service');
        $projectService = $this->get('app.service.projects_service');
        /** @var User $user */
        $user = $this->getUser();
        $userType = $user->getType();
        $em = $this->getDoctrine()->getManager();
        $projects = $em->getRepository('AppBundle:Project')->findExecutionerProjects();
        $projects = array_reverse($projects);
        $filteredProjects = $projectService->filterProjects($projects,$user,"LittleBoss");
        foreach ($filteredProjects as $project){

            /** @var Project $project */
            $comments = $this->getDoctrine()
                ->getRepository('AppBundle:Comments')
                ->findByProjectID($project->getId());
            $project->setComments($commentsService->filterComments($comments,$user));
        }
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
        $commentsService = $this->get('app.service.comments_service');
        $projectService = $this->get('app.service.projects_service');
        /** @var User $user */
        $user = $this->getUser();
        $userType = $user->getType();
        $em = $this->getDoctrine()->getManager();
        $projects = $em->getRepository('AppBundle:Project')->findAll();
        $projects = array_reverse($projects);
        $filteredProjects = $projectService->filterProjects($projects,$user,$userType);
        foreach ($filteredProjects as $project){
            /** @var Project $project */
            $comments = $this->getDoctrine()
                ->getRepository('AppBundle:Comments')
                ->findByProjectID($project->getId());
            $project->setComments($commentsService->filterComments($comments,$user));
        }
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
        $this->checkCredentials(array("Manager","LittleBoss","Boss"));
        /** @var  $user User */
        $user = $this->getUser();
        $project = new Project();
        $form = $this->createForm('AppBundle\Form\ProjectType', $project);
        if($user->getType() != "LittleBoss"){
            $form->remove("designer");
            $form->remove("executioner");
            if($user->getUsername() == 'winbet.online') {
                $form->add('designer', ChoiceType::class, array('label' => "Дизайнер",
                    "required" => false,
                    'choices' => array(
                        "Няма дизайнер" => "Няма дизайнер",
                        "Михаил Станев" => "Михаил Станев"
                    ),
                    'data' => $project->getDesigner()
                ));
            }
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                $projectService = $this->get('app.service.projects_service');
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
    public function showAction(Request $request, Project $project)
    {

        //this function returns "" if the user is allowed and if not returns $this->render
        $forbidden = $this->checkCredentials("all");
        if($forbidden){
            return $forbidden;
        }
        /**
         * @var $user User
         */
        $commentsService = $this->get('app.service.comments_service');
        $user = $this->getUser();
        $userType = $user->getType();
        $deleteForm = $this->createDeleteForm($project);
        $comments = $this->getDoctrine()
            ->getRepository('AppBundle:Comments')
            ->findByProjectID($project->getId());
        $comments = $commentsService->filterComments($comments,$user);
        if ($userType == "LittleBoss" && !$project->isSeenByLittleBoss()) {
            $project->setSeenByLittleBoss(true);
        }elseif ($userType == "Designer" && !$project->isSeenByDesigner()) {
            $project->setDesignerAccepted(true);
            $project->setDateDesigner(new \DateTime());
        }elseif ($userType == "Executioner" && !$project->isSeenByExecutioner()) {
            $project->setExecutionerAccepted(true);
            $project->setDateExecutioner(new \DateTime());
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($project);
        $em->flush();
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
        if($forbidden){
            return $forbidden;
        }
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
        if($userType != "LittleBoss"){
            $editForm->remove('designer');
            $editForm->remove("executioner");
        }
        if($userType == "Designer"){
            $editForm->remove('description');
            $editForm->remove('term');
            $editForm->remove('fromUser');
            $editForm->remove('department');
            $editForm->remove('urgent');

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
        if($forbidden){
            return $forbidden;
        }
        $form = $this->createDeleteForm($project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($project);
            $em->flush();
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
    public function updateAction(Request $request, Project $project){
        //this function returns "" if the user is allowed and if not returns $this->render
        $forbidden = $this->checkCredentials("all");
        if($forbidden){
            return $forbidden;
        }
        $referer = $request->headers->get('referer');
        /** @var User $user */
        $user = $this->getUser();
        if (isset($_POST['approve'])) {
            $project->setApproved(true);
            $project->setRejected(false);
            $project->setDesignerFinishedDate(new \DateTime());
            $project->setForApproval(false);
            $project->setHold(false);
            $this->get('session')->getFlashBag()->set('success', "Успешно одобрихте заявката!");
        } elseif (isset($_POST['reject'])) {
            $comment =  new Comments();
            $comment->setProjectID($project->getId());
            $comment->setContent($_POST['rejectComment']);
            $comment->setToUser("Designer");
            $commentsService = $this->get('app.service.comments_service');
            $commentsService->newComment($comment,$user,new \DateTime());
            $project->setRejected(true);
            $project->setApproved(false);
            $project->setForApproval(false);
            $project->setHold(false);
            $this->get('session')->getFlashBag()->set('success', "Успешно отхвърлихте заявката!");
        } elseif (isset($_POST['archive'])) {
            $project->setIsOver(true);
            $project->setOverDate(new \DateTime());
            $project->setForApproval(true);
            $project->setHold(false);
            $project->setRejected(false);
            $this->get('session')->getFlashBag()->set('success', "Успешно архивирахте заявката!");
        }elseif(isset($_POST['hold'])){
            $project->setHold(true);
            $project->setRejected(false);
            $project->setForApproval(false);
            $project->setApproved(false);
            $this->get('session')->getFlashBag()->set('success', "Заявката успешно сложена на изчакване!");

        }elseif(isset($_POST['forApproval'])){
            $project->setForApproval(true);
            $project->setRejected(false);
            $project->setHold(false);
            $project->setApproved(false);
            $this->get('session')->getFlashBag()->set('success', "Заявката успешно сложена за одобрение!");
        }elseif(isset($_POST['working'])){
            $em = $this->getDoctrine()->getManager();
            $projects = $em->getRepository('AppBundle:Project')->findDesignerProjects($user->getFullName());
            if($projects){
                foreach ($projects as $singleProject){
                    /** @var  $singleProject Project */
                    $singleProject->setWorking(false);
                }
            }
            $project->setWorking(true);
        }elseif(isset($_POST['link'])){
            $project->setDesignerLink($_POST['link']);
        }
        elseif(isset($_POST['rejectFile'])){
            $file = $this->getDoctrine()->getManager()->getRepository('AppBundle:Files')->find($_POST['rejectFile']);
            $file->setRejected(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($file);
            $em->flush();

        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($project);
        $em->flush();

        return $this->redirect($referer);
    }
    private function checkCredentials($allowedUserRoles){
        $authenticationUtils = $this->get('security.authentication_utils');
        $lastUsername = $authenticationUtils->getLastUsername();
        /** @var User $user */
        $user = $this->getUser();
        if($user){
            if($allowedUserRoles == "all"){
                return "";
            }
            foreach (explode(" ",$user->getRole()) as $role){
                if(in_array($role,$allowedUserRoles)){
                    return "";
                }
            }
            $this->get('session')->getFlashBag()->set('error', "Нямате достъп до тази страница!");
            return  $this->render('::base.html.twig');
        }
        $this->get('session')->getFlashBag()->set('error', "Моля, първо влезте в профила си!");
        return  $this->render('@App/Security/login.html.twig',array(
            'last_username'=> $lastUsername
        ));
    }
    /**
     *Uploads images
     *
     * @Route("/{id}/imageUpload", name="image_upload")
     * @Method("POST")
     */
    public function uploadImage(Request $request, Project $project){
        //this function returns "" if the user is allowed and if not returns $this->render
        $forbidden = $this->checkCredentials(array("Designer","LittleBoss","Manager","Executioner"));
        if($forbidden){
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
        $this->get('session')->getFlashBag()->set('success', 'Файловете успешно качени!');
        return $this->redirect($referer);
    }
    public function sortProjects(Project $a,Project $b)
    {
        $projectAOver = strtotime($a->getOverDate()->format("Y-m-d"));
        $projectBOver = strtotime($b->getOverDate()->format("Y-m-d"));
        return $projectBOver - $projectAOver;
    }
	
}

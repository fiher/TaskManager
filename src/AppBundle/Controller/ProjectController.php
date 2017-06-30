<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Comments;
use AppBundle\Entity\User;
use AppBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
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
    const NO_TERM_DEFAULT_VALUE = '2090-03-19';
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
            }elseif($project->getDesigner() == $user->getUsername()
                || $project->getExecutioner() == $user->getUsername()
                || $project->getFromUser() == $user->getUsername()){
                $filteredProjects[] = $project;
            }

        }
        $filteredProjects = array_reverse($filteredProjects);
        return $this->render('project/index.html.twig', array(
            'projects' => $filteredProjects,
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
        return $this->render('project/index.html.twig', array(
            'projects' => $filteredProjects,
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
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                $projectService = $this->get('app.service.projects_service');
                $managerFiles = $managerFiles = $request->files->get('appbundle_project')['managerFiles'];
                $isWithoutTerm = array_key_exists('noTerm', $request->request->get('appbundle_project'));

                    $projectService->createProject($project, $user, $isWithoutTerm, self::NO_TERM_DEFAULT_VALUE);
                    $filesService = $this->get('app.service.files_service');
                    foreach ($managerFiles as $managerFile) {
                        $fileName = $filesService->uploadFileAndReturnName($managerFile,$this->getParameter('files_directory'));
                        $filesService->createFile($fileName, $project, $user);

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
        $commentsService = $this->get('app.service.comments_service');
        $errorMessage = "";
        $successMessage = "";
        $user = $this->getUser();
        $userType = $user->getType();
        $deleteForm = $this->createDeleteForm($project);
        $comments = $this->getDoctrine()
            ->getRepository('AppBundle:Comments')
            ->findByProjectID($project->getId());
        $comments = $commentsService->filterComments($comments,$user);
        if ($userType == "LittleBoss" && !$project->isSeenByLittleBoss()) {
            $project->setSeenByLittleBoss(true);
        } elseif ($userType == "Manager" && !$project->isSeenByManager()) {
            $project->setSeenByManager(true);
        } elseif ($userType == "Designer" && !$project->isSeenByDesigner()) {
            $project->setDesignerAccepted(true);
            $project->setDateDesigner(new \DateTime());
            $project->setSeenByDesigner(true);
        } elseif ($userType == "Executioner" && !$project->isSeenByExecutioner()) {
            $project->setExecutionerAccepted(true);
            $project->setDateExecutioner(new \DateTime());
            $project->setSeenByExecutioner(true);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($project);
        $em->flush();
        $comment = new Comments();

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
            'errorMessage' => $errorMessage,
            'successMessage' => $successMessage,
            'designerFiles' => $project->getDesignerFiles(),
            'managerFiles' => $project->getManagerFiles()
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
        $successMessage = "";
        $deleteForm = $this->createDeleteForm($project);
        $project->setHold(false);
        $editForm = $this->createForm('AppBundle\Form\ProjectType', $project);
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
            $successMessage = "Успешно променихте заявката!";
            return $this->redirectToRoute('project_show', array('id' => $project->getId()));
        }
        return $this->render('project/edit.html.twig', array(
            'project' => $project,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'successMessage'=>$successMessage
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
        $this->checkCredentials(array("Manager","LittleBoss","Boss"));

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
        /**@var Project $project */
        dump($request);
        if (isset($_POST['approve'])) {
            $project->setApproved(true);
            $project->setRejected(false);
            $project->setDesignerFinishedDate(new \DateTime());
            $project->setForApproval(false);
            $project->setHold(false);
            $successMessage = "Успешно одобрихте заявката!";
        } elseif (isset($_POST['reject'])) {
            $successMessage = "Успешно отхвърлихте заявката!";
            $project->setRejected(true);
            $project->setApproved(false);
            $project->setForApproval(false);
            $project->setHold(false);
        } elseif (isset($_POST['archive'])) {
            if ($project->isApproved()) {
                $successMessage = "Успешно архивирахте заявката!";
                $project->setIsOver(true);
                $project->setOverDate(new \DateTime());
                $project->setForApproval(true);
                $project->setHold(false);
                $project->setRejected(false);
            } else {
                $errorMessage = "Не можете да архивирате заявка, която не е одобрена!";
            }
        }elseif(isset($_POST['hold'])){
            $project->setHold(true);
            $project->setRejected(false);
            $project->setForApproval(false);
            $project->setApproved(false);

        }elseif(isset($_POST['forApproval'])){
            $project->setForApproval(true);
            $project->setRejected(false);
            $project->setHold(false);
            $project->setApproved(false);
        }elseif(isset($_POST['working'])){
            $em = $this->getDoctrine()->getManager();
            $query = $em->getRepository('AppBundle:Project')->
            createQueryBuilder('project')->
            where('project.id != currentId')->setParameter('currentId',$project->getId())->getQuery();
            $projects = $query->getResult();
            if($projects){
                foreach ($projects as $singleProject){
                    /** @var  $singleProject Project */

                }
            }
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($project);
        $em->flush();
        $requestURL = explode("/",$request->getUri())[5];
        if($requestURL== "update") {
            //return $this->redirectToRoute("project_index");
        }else{
            //return $this->redirectToRoute("project_show", array('id' => $project->getId()
            //));
        }
    }

    private function checkCredentials($allowedUserRoles){
        $authenticationUtils = $this->get('security.authentication_utils');
        $lastUsername = $authenticationUtils->getLastUsername();

        /** @var User $user */
        $user = $this->getUser();
        $error = $authenticationUtils->getLastAuthenticationError();
        if($user){
            if($allowedUserRoles == "all"){
                return "";
            }
            foreach (explode(" ",$user->getRole()) as $role){
                if(in_array($role,$allowedUserRoles)){
                    return "";
                }
            }
            return  $this->render('::base.html.twig',array(
                'errorMessage'=>"Нямате достъп до тази страница!",
                'successMessage'=>""
            ));
        }
        return  $this->render('@App/Security/login.html.twig',array(
            'errorMessage'=>"Моля първо влезте в профила си!",
            'successMessage'=>"",
            'last_username'=> $lastUsername
        ));
    }

}

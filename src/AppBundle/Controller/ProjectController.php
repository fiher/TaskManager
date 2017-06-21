<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Comments;
use AppBundle\Entity\User;
use AppBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilder;
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
        /** @var User $user */
        $user = $this->getUser();

        $userType = $user->getType();
        $em = $this->getDoctrine()->getManager();
        $projects = $em->getRepository('AppBundle:Project')->findBy(array(
            'isOver'=>true
        ));
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
        /** @var User $user */
        $user = $this->getUser();
        $userType = $user->getType();
        $em = $this->getDoctrine()->getManager();
        $projects = $em->getRepository('AppBundle:Project')->findAll();
        $projects = array_reverse($projects);
        $filteredProjects = $this->filterProjects($projects,$user,$userType);
        foreach ($filteredProjects as $project){
            /** @var Project $project */
            $comments = $this->getDoctrine()
                ->getRepository('AppBundle:Comments')
                ->findBy(
                    array("zadanieID" => $project->getId())
                );
            $project->setComments($commentsService->filterComments($comments,$user));
        }
        return $this->render('project/index.html.twig', array(
            'projects' => $filteredProjects,
        ));
    }

    private function filterProjects($projects,$user,$userType){
        $filteredProjects = [];
        /**
         * @var $project Project
         */
        foreach ($projects as $project){
            if($project->getIsOver()){
                continue;
            }
            /**
             * @var Project $project
             * @var $user User
             */
            $functionName = "isSeenBy".$userType;
            if($user->getUsername() == "sky.stroy"){
                if($project->getFromUser() == "sky.stroy"){
                    $functionName = "isSeenByManager";
                }elseif ($project->getExecutioner()== "sky.stroy"){
                    $functionName = "isSeenByExecutioner";
                }
            }
            if($functionName == "isSeenByManager"){
                $functionName = "isSeenByLittleBoss";
            }
            if($project->$functionName()){
                $project->setClass("seen");
                $project->setStatus("Видяно");
            }else{
                $project->setClass("notSeen");
                $project->setStatus("Не е видяно");
            }

            if ($project->getClass() == "seen"&& !$project->getDesigner()){
                $project->setClass("seenNotAsssigned");
                $project->setStatus("Видяно, но неразпределено");
            }elseif ($project->getClass() == "seen"&& $project->getDesigner()){
                $project->setClass("assigned");
                $project->setStatus("Разпределено");
            }
            $now = strtotime(date('Y-m-d H:i:s'));
            $term = strtotime($project->getTerm()->format("Y-m-d H:i:s"));

            $datediff = $term - $now;
            $datediff = floor($datediff / (60 * 60 * 24));
            $createdDate = strtotime($project->getDate()->format("Y-m-d"));
            $diffCreatedToday = $term - $createdDate;
            $diffCreatedToday = floor($diffCreatedToday / (60 * 60 * 24));if($diffCreatedToday<= 1){
                $project->setErgent(true);
            }

            if(!in_array("Manager",explode(" ",$user->getRole()))) {
                if($datediff<=1){
                    $project->setClass("due");
                    $project->setStatus("Изтичащ срок");
                }
                if($project->isApproved()) {
                    $project->setClass("approved");
                    $project->setStatus("Одобрено");
                    $project->setErgent(false);

                }if($project->isRejected()){
                    $project->setStatus("Отхвърлено");
                    $project->setClass("rejected");
                }
                if ($project->isErgent()) {
                    $project->setClass($project->getClass() . " urgent");
                }
            }
            if($project->isHold()){
                $project->setClass("onHold");
                $project->setStatus("Изчакване");
            }
            if($project->isForApproval()){
                $project->setClass("forApproval");
                $project->setStatus("За одобрение");
            }
            if($userType != "LittleBoss" && $userType != "Boss"){
                if($userType == "Designer" && $user->getUsername() == $project->getDesigner()){
                    $filteredProjects[] = $project;
                }elseif($userType == "Executioner" && $user->getUsername() == $project->getExecutioner()){
                    $filteredProjects[] = $project;
                }elseif ($userType == "Manager" && $user->getUsername() == $project->getFromUser()){
                    $filteredProjects[] = $project;
                }
            }else {
                $filteredProjects[] = $project;
            }
        }
        return $filteredProjects;
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
        //$project->setFromUser($user->getUsername());
        //$project->setDepartment($user->getDepartment());
        $project->setIsOver(false);
        $project->setDate(new \DateTime());
        $project->setSeenByDesigner(false);
        $project->setSeenByExecutioner(false);
        $project->setSeenByLittleBoss(false);
        $project->setSeenByManager(false);
        if($project->getTerm() == $project->getDate()){
            $project->setErgent(true);
        }
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

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
            ->findBy(
                array("zadanieID" => $project->getId())
            );
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
            'successMessage' => $successMessage
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
        $forbidden = $this->checkCredentials(array("Manager","LittleBoss","Boss","Designer"));
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
            $editForm->remove('ergent');

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
            return $this->redirectToRoute("project_index");
        }else{
            return $this->redirectToRoute("project_show", array('id' => $project->getId()));
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

<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Comments;
use AppBundle\Entity\User;
use AppBundle\Entity\Zadanie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Zadanie controller.
 *
 * @Route("zadanie")
 */
class ZadanieController extends Controller
{
    /**
     * Lists all zadanie entities.
     *
     * @Route("/archive", name="zadanie_archive")
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
        $zadanies = $em->getRepository('AppBundle:Zadanie')->findBy(array(
            'isOver'=>true
        ));
        $filteredZadanies = [];
        foreach ($zadanies as $zadanie){
            /** @var $zadanie Zadanie */
            $zadanie->setClass("archive");
            $zadanie->setStatus("Приключено");
            if($user->getType() == "LittleBoss"){
                $filteredZadanies[] = $zadanie;
            }elseif($zadanie->getDesigner() == $user->getUsername()
                || $zadanie->getExecutioner() == $user->getUsername()
                || $zadanie->getFromUser() == $user->getUsername()){
                $filteredZadanies[] = $zadanie;
            }

        }
        $filteredZadanies = array_reverse($filteredZadanies);
        return $this->render('zadanie/index.html.twig', array(
            'zadanies' => $filteredZadanies,
        ));
    }


    /**
     * Lists all zadanie entities.
     *
     * @Route("/", name="zadanie_index")
     * @Method("GET")
     */

    public function indexAction()
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
        $zadanies = $em->getRepository('AppBundle:Zadanie')->findAll();
        $zadanies = array_reverse($zadanies);
        $filteredZadanies = $this->filterZadanies($zadanies,$user,$userType);
        return $this->render('zadanie/index.html.twig', array(
            'zadanies' => $filteredZadanies,
        ));
    }

    private function filterZadanies($zadanies,$user,$userType){
        $filteredZadanies = [];
        /**
         * @var $zadanie Zadanie
         */
        foreach ($zadanies as $zadanie){
            if($zadanie->getIsOver()){
                continue;
            }
            /**
             * @var Zadanie $zadanie
             *@var $user User
             */
            $functionName = "isSeenBy".$userType;
            if($user->getUsername() == "sky.stroy"){
                if($zadanie->getFromUser() == "sky.stroy"){
                    $functionName = "isSeenByManager";
                }elseif ($zadanie->getExecutioner()== "sky.stroy"){
                    $functionName = "isSeenByExecutioner";
                }
            }
            if($functionName == "isSeenByManager"){
                $functionName = "isSeenByLittleBoss";
            }
            if($zadanie->$functionName()){
                $zadanie->setClass("seen");
                $zadanie->setStatus("Видяно");
            }else{
                $zadanie->setClass("notSeen");
                $zadanie->setStatus("Не е видяно");
            }

            if ($zadanie->getClass() == "seen"&& !$zadanie->getDesigner()){
                $zadanie->setClass("seenNotAsssigned");
                $zadanie->setStatus("Видяно, но неразпределено");
            }elseif ($zadanie->getClass() == "seen"&& $zadanie->getDesigner()){
                $zadanie->setClass("assigned");
                $zadanie->setStatus("Разпределено");
            }
            $now = strtotime(date('Y-m-d H:i:s'));
            $term = strtotime($zadanie->getTerm()->format("Y-m-d H:i:s"));

            $datediff = $term - $now;
            $datediff = floor($datediff / (60 * 60 * 24));
            $createdDate = strtotime($zadanie->getDate()->format("Y-m-d"));
            $diffCreatedToday = $term - $createdDate;
            $diffCreatedToday = floor($diffCreatedToday / (60 * 60 * 24));
            dump($diffCreatedToday);
            if($diffCreatedToday<= 1){
                $zadanie->setErgent(true);
            }

            if(!in_array("Manager",explode(" ",$user->getRole()))) {
                if($datediff<=1){
                    $zadanie->setClass("due");
                    $zadanie->setStatus("Изтичащ срок");
                }
                if($zadanie->isApproved()) {
                    $zadanie->setClass("approved");
                    $zadanie->setStatus("Одобрено");
                    $zadanie->setErgent(false);

                }if($zadanie->isRejected()){
                    $zadanie->setStatus("Отхвърлено");
                    $zadanie->setClass("rejected");
                }
                if ($zadanie->isErgent()) {
                    $zadanie->setClass($zadanie->getClass() . " urgent");
                }
            }
            if($zadanie->isHold()){
                $zadanie->setClass("onHold");
                $zadanie->setStatus("Изчакване");
            }
            if($zadanie->isForApproval()){
                $zadanie->setClass("forApproval");
                $zadanie->setStatus("За одобрение");
            }
            if($userType != "LittleBoss" && $userType != "Boss"){
                if($userType == "Designer" && $user->getUsername() == $zadanie->getDesigner()){
                    $filteredZadanies[] = $zadanie;
                }elseif($userType == "Executioner" && $user->getUsername() == $zadanie->getExecutioner()){
                    $filteredZadanies[] = $zadanie;
                }elseif ($userType == "Manager" && $user->getUsername() == $zadanie->getFromUser()){
                    $filteredZadanies[] = $zadanie;
                }
            }else {
                $filteredZadanies[] = $zadanie;
            }
        }
        return $filteredZadanies;
    }
    /**
     * Creates a new zadanie entity.
     *
     * @Route("/new", name="zadanie_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        //this function returns "" if the user is allowed and if not returns $this->render
        $this->checkCredentials(array("Manager","LittleBoss","Boss"));

        /** @var  $user User */
        $user = $this->getUser();
        $zadanie = new Zadanie();
        $form = $this->createForm('AppBundle\Form\ZadanieType', $zadanie);
        if($user->getType() != "LittleBoss"){
            $form->remove("designer");
            $form->remove("executioner");
        }
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
        //$zadanie->setFromUser($user->getUsername());
        //$zadanie->setDepartment($user->getDepartment());
        $zadanie->setIsOver(false);
        $zadanie->setDate(new \DateTime());
        $zadanie->setSeenByDesigner(false);
        $zadanie->setSeenByExecutioner(false);
        $zadanie->setSeenByLittleBoss(false);
        $zadanie->setSeenByManager(false);
        if($zadanie->getTerm() == $zadanie->getDate()){
            $zadanie->setErgent(true);
        }
            $em = $this->getDoctrine()->getManager();
            $em->persist($zadanie);
            $em->flush();

            return $this->redirectToRoute('zadanie_show', array('id' => $zadanie->getId()));
        }

        return $this->render('zadanie/new.html.twig', array(
            'zadanie' => $zadanie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a zadanie entity.
     *
     * @Route("/{id}", name="zadanie_show")
     * @Method({"GET", "POST"})
     */
    public function showAction(Zadanie $zadanie)
    {
        //this function returns "" if the user is allowed and if not returns $this->render
        $forbidden = $this->checkCredentials("all");
        if($forbidden){
            return $forbidden;
        }
        /**
         * @var $user User
         */
        $errorMessage = "";
        $successMessage = "";
        $user = $this->getUser();
        $userType = $user->getType();
        $deleteForm = $this->createDeleteForm($zadanie);
        $comments = $this->getDoctrine()
            ->getRepository('AppBundle:Comments')
            ->findBy(
                array("zadanieID" => $zadanie->getId())
            );
        $comments = $this->filterComments($comments);
        if ($userType == "LittleBoss" && $zadanie->isSeenByLittleBoss()) {
            $zadanie->setSeenByLittleBoss(true);
        } elseif ($userType == "Manager" && $zadanie->isSeenByManager()) {
            $zadanie->setSeenByManager(true);
        } elseif ($userType == "Designer" && !$zadanie->isSeenByDesigner()) {
            $zadanie->setDesignerAccepted(true);
            $zadanie->setDateDesigner(new \DateTime());
            $zadanie->setSeenByDesigner(true);
        } elseif ($userType == "Executioner" && !$zadanie->isSeenByExecutioner()) {
            $zadanie->setExecutionerAccepted(true);
            $zadanie->setDateExecutioner(new \DateTime());
            $zadanie->setSeenByExecutioner(true);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($zadanie);
        $em->flush();
        $comment = new Comments();
        $form = $this->createForm('AppBundle\Form\CommentsType', $comment);
        if ($user->getType() != "LittleBoss") {
            $form->remove("toUser");
        }
        return $this->render('zadanie/show.html.twig', array(
            'zadanie' => $zadanie,
            'delete_form' => $deleteForm->createView(),
            'comment' => $comment,
            'comments' => $comments,
            'form' => $form->createView(),
            'errorMessage' => $errorMessage,
            'successMessage' => $successMessage
        ));
    }

    function filterComments($comments){
        /**
         * @var $comment Comments
         * @var $user User
         */
        $user = $this->getUser();
        if($user->getType() != "LittleBoss") {
            for ($i = 0; $i < count($comments); $i++) {
                $comment = $comments[$i];
                if ($comment->getCreatorRole() == "LittleBoss") {
                    if (!in_array($comment->getToUser(), explode(" ", $user->getRole()))) {
                        unset($comments[$i]);
                        $i--;
                        $comments = array_values($comments);
                    }
                } else {
                    if ($comment->getCreatorRole() != $user->getType()) {
                        unset($comments[$i]);
                        $i--;
                        $comments = array_values($comments);
                    }
                }
            }
        }
        foreach ($comments as $comment){
            $comment->setClass($comment->getCreatorRole());
        }
        return $comments;

    }
    /**
     * Displays a form to edit an existing zadanie entity.
     *
     * @Route("/{id}/edit", name="zadanie_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Zadanie $zadanie)
    {
        //this function returns "" if the user is allowed and if not returns $this->render
        $forbidden = $this->checkCredentials(array("Manager","LittleBoss","Boss","Designer"));
        if($forbidden){
            return $forbidden;
        }
        $userType = $this->getUser()->getType();
        $successMessage = "";
        $deleteForm = $this->createDeleteForm($zadanie);
        $zadanie->setHold(false);
        $editForm = $this->createForm('AppBundle\Form\ZadanieType', $zadanie);
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
            return $this->redirectToRoute('zadanie_show', array('id' => $zadanie->getId()));
        }
        return $this->render('zadanie/edit.html.twig', array(
            'zadanie' => $zadanie,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'successMessage'=>$successMessage
        ));
    }

    /**
     * Deletes a zadanie entity.
     *
     * @Route("/{id}", name="zadanie_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Zadanie $zadanie)
    {
        $this->checkCredentials(array("Manager","LittleBoss","Boss"));

        $form = $this->createDeleteForm($zadanie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($zadanie);
            $em->flush();
        }

        return $this->redirectToRoute('zadanie_index');
    }

    /**
     * Creates a form to delete a zadanie entity.
     *
     * @param Zadanie $zadanie The zadanie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Zadanie $zadanie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('zadanie_delete', array('id' => $zadanie->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     *Updates zadanie by one property only.
     *
     * @Route("/{id}/update", name="zadanie_update")
     * @Route("/{id}/fastupdate", name="zadanie_fast_update")
     * @Method("POST")
     */
    public function updateAction(Request $request, Zadanie $zadanie){
        /**@var Zadanie $zadanie */
        if (isset($_POST['approve'])) {
            $zadanie->setApproved(true);
            $zadanie->setRejected(false);
            $zadanie->setDesignerFinishedDate(new \DateTime());
            $zadanie->setForApproval(false);
            $zadanie->setHold(false);
            $successMessage = "Успешно одобрихте заявката!";
        } elseif (isset($_POST['reject'])) {
            $successMessage = "Успешно отхвърлихте заявката!";
            $zadanie->setRejected(true);
            $zadanie->setApproved(false);
            $zadanie->setForApproval(false);
            $zadanie->setHold(false);
        } elseif (isset($_POST['archive'])) {
            if ($zadanie->isApproved()) {
                $successMessage = "Успешно архивирахте заявката!";
                $zadanie->setIsOver(true);
                $zadanie->setOverDate(new \DateTime());
                $zadanie->setForApproval(true);
                $zadanie->setHold(false);
                $zadanie->setRejected(false);
            } else {
                $errorMessage = "Не можете да архивирате заявка, която не е одобрена!";
            }
        }elseif(isset($_POST['hold'])){
            $zadanie->setHold(true);
            $zadanie->setRejected(false);
            $zadanie->setForApproval(false);
            $zadanie->setApproved(false);

        }elseif(isset($_POST['forApproval'])){
            $zadanie->setForApproval(true);
            $zadanie->setRejected(false);
            $zadanie->setHold(false);
            $zadanie->setApproved(false);
        }elseif(isset($_POST['working'])){
            $em = $this->getDoctrine()->getManager();
            $query = $em->getRepository('AppBundle:Zadanie')->
            createQueryBuilder('zadanie')->
            where('zadanie.id != currentId')->setParameter('currentId',$zadanie->getId())->getQuery();
            $zadanies = $query->getResult();
            if($zadanies){
                foreach ($zadanies as $singleZadanie){
                    /** @var  $singleZadanie Zadanie */

                }
            }
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($zadanie);
        $em->flush();
        $requestURL = explode("/",$request->getUri())[5];
        if($requestURL== "update") {
            return $this->redirectToRoute("zadanie_index");
        }else{
            return $this->redirectToRoute("zadanie_show", array('id' => $zadanie->getId()));
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

<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 21-Jun-17
 * Time: 11:50
 */

namespace AppBundle\Service;


use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Repository\CommentsRepository;
use AppBundle\Repository\ProjectRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Session\Session;

class ProjectService
{
    private $entityManager;
    private $session;
    private $manager;
    private $commentsService;
    public function __construct(
        EntityManagerInterface $entityManager,
        Session $session,
        ManagerRegistry $manager,CommentsService $commentsService)
    {
        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->manager = $manager;
        $this->commentsService = $commentsService;
        $this->projectRepository = $this->manager->getRepository('AppBundle:Project');
        $this->commentsRepository = $this->manager->getRepository('AppBundle:Comments');
    }
    public function filterProjects($projects,$user,$userType){
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

            if($project->isSeenByLittleBoss()){
                $project->setClass("seen");
                $project->setStatus("Видяно");
            }else{
                $project->setClass("notSeen");
                $project->setStatus("Не е видяно");
            }
            if($user->getUsername() == 'm.stanev'){
                $project->setClass("assigned");
                $project->setStatus("Разпределено");
            }

            if ($project->getClass() == "seen"&& !$project->getDesigner()){
                $project->setClass("seenNotAsssigned");
                $project->setStatus("Видяно, но неразпределено");
            }elseif ($project->getClass() == "seen"&& $project->getDesigner()){
                $project->setClass("assigned");
                $project->setStatus("Разпределено");
            }
            if(!$project->isWithoutTerm()){
                $now = strtotime(date('Y-m-d H:i:s'));
                $term = strtotime($project->getTerm()->format("Y-m-d H:i:s"));

                $datediff = $term - $now;
                $datediff = floor($datediff / (60 * 60 * 24));
                $createdDate = strtotime($project->getDate()->format("Y-m-d"));
                $diffCreatedToday = $term - $createdDate;
                $diffCreatedToday = floor($diffCreatedToday / (60 * 60 * 24));
                if($diffCreatedToday<= 1){
                    $project->setUrgent(true);
                }
                if(!in_array("Manager",explode(" ",$user->getRole()))) {
                    if($datediff<=1){
                        $project->setClass("due");
                        $project->setStatus("Изтичащ срок");
                    }
                }
            }
            if(!in_array("Manager",explode(" ",$user->getRole()))) {
                if($project->isApproved()) {
                    $project->setClass("approved");
                    $project->setStatus("Одобрено");
                    $project->setUrgent(false);

                }if($project->isRejected()){
                    $project->setStatus("Отхвърлено");
                    $project->setClass("rejected");
                }
                if ($project->isUrgent()) {
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
            if($project->isWorking()){
                $project->setClass('working');
                $project->setStatus('Дизайнера работи по тази заявка');
            }
            if($project->getDesigner() == 'Михаил Станев' && $user->getUsername() == 'winbet.online'){
                if($project->isSeenByDesigner()){
                    $project->setClass('seen');
                    $project->setStatus('Видяно');
                }
                if($project->isApproved()) {
                    $project->setClass("approved");
                    $project->setStatus("Одобрено");
                    $project->setUrgent(false);

                }if($project->isRejected()){
                    $project->setStatus("Отхвърлено");
                    $project->setClass("rejected");
                }
            }

            if($userType != "LittleBoss" && $userType != "Boss"){
                if(($userType == "Designer" && $user->getFullName() == $project->getDesigner() && !$project->isApproved()) ||
                    ($userType == "Designer" && $user->getFullName() == $project->getSecondDesigner() && !$project->isApproved()) ||
                    ($userType = "Executioner" && $user->getFullName() == $project->getExecutioner()) ||
                    ($userType = "Manager" && $user->getFullName() == $project->getFromUser())){
                    $filteredProjects[] = $project;
                }
            }else {
                $filteredProjects[] = $project;
            }
        }
        //usort($filteredProjects, array($this, "sortProjects"));
        return $filteredProjects;
    }
    public function sortProjects(Project $a,Project $b)
    {
        $projectATerm = strtotime($a->getTerm()->format("Y-m-d"));
        $projectBTerm = strtotime($b->getTerm()->format("Y-m-d"));
        return $projectATerm - $projectBTerm;
    }
    public function createProject(Project $project, User $user){
        $project->setFromUser($user->getFullName());
        $project->setDepartment($user->getDepartment());
        $project->setIsOver(false);
        $project->setDate(new \DateTime());
        $project->setSeenByDesigner(false);
        $project->setSeenByExecutioner(false);
        $project->setSeenByLittleBoss(false);
        $project->setSeenByManager(false);
        if($project->getTerm() == $project->getDate()){
            $project->setUrgent(true);
        }
        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }
    public function setProject(Project $project, User $user) {
        $userType = $user->getType();
        if ($userType == "LittleBoss" && !$project->isSeenByLittleBoss()) {
            $project->setSeenByLittleBoss(true);
        }elseif ($userType == "Designer" && !$project->isSeenByDesigner()) {
            $project->setDesignerAccepted(true);
            $project->setDateDesigner(new \DateTime());
        }elseif ($userType == "Executioner" && !$project->isSeenByExecutioner()) {
            $project->setExecutionerAccepted(true);
            $project->setDateExecutioner(new \DateTime());
        }
        return $project;
    }
    public function flushProject(Project $project) {
        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }
    public function removeFormFieldsForDesigners($form) {
        /** @var FormBuilder $form */
        $form->remove('description');
        $form->remove('term');
        $form->remove('fromUser');
        $form->remove('department');
        $form->remove('urgent');

        return $form;
    }
    public function removeFormFieldsForManagers($form) {
        /** @var FormBuilder $form */
        $form->remove('designer');
        $form->remove("executioner");

        return $form;
    }
    public function addDesignerFieldForManagers($form, $data){
        /** @var FormBuilder $form */
        $form->add('designer', ChoiceType::class, array('label' => "Дизайнер",
            "required" => false,
            'choices' => array(
                "Няма дизайнер" => "Няма дизайнер",
                "Михаил Станев" => "Михаил Станев"
            ),
            'data' => $data
        ));
        return $form;
    }
    public function getDesignerProjects($fullName) {
        return $this->projectRepository->findDesignerProjects($fullName);
    }
    public function addCommentsToProjects (array $projects, User $user) {
        foreach ($projects as $project){
            /** @var Project $project */
            $comments = $this->commentsRepository->findByProjectID($project->getId());
            $project->setComments($this->commentsService->filterComments($comments,$user));
        }
        return $projects;
    }
    public function addSecondDesignerField ($form, $data) {
        /** @var FormBuilder $form */
        $form->add('second_designer',ChoiceType::class,array('label'=>" Помощник Дизайнер",
            "required"=>false,
            'choices'=>array(
                "Няма дизайнер"=>"Няма дизайнер",
                "Александра Вали" => "Александра Вали",
                "Йоана Борисова" => "Йоана Борисова",
                "Рената Дудлей" => "Рената Дудлей",
                "Михаил Станев" => "Михаил Станев"
            ),
            'data'=>$data
        ));
        return $form;
    }
}
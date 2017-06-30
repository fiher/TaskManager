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
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
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
                $project->setUrgent(true);
            }

            if(!in_array("Manager",explode(" ",$user->getRole()))) {
                if($datediff<=1){
                    $project->setClass("due");
                    $project->setStatus("Изтичащ срок");
                }
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
            if($userType != "LittleBoss" && $userType != "Boss"){
                if($userType == "Designer" && $user->getFullName() == $project->getDesigner()){
                    $filteredProjects[] = $project;
                }elseif($userType == "Executioner" && $user->getFullName() == $project->getExecutioner()){
                    $filteredProjects[] = $project;
                }elseif ($userType == "Manager" && $user->getFullName() == $project->getFromUser()){
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
    private function isWithoutDate(\DateTime $term,$defaultValue){
        $term = $term->format("j-M-Y");
        if($term == $defaultValue){
            return true;
        }else{
            return false;
        }
    }
    public function createProject(Project $project, User $user,bool $isWithoutTerm,string $noTermDefaultValue){
        $project->setFromUser($user->getFullName());
        $project->setDepartment($user->getDepartment());
        $project->setIsOver(false);
        $project->setDate(new \DateTime());
        $project->setSeenByDesigner(false);
        $project->setSeenByExecutioner(false);
        $project->setSeenByLittleBoss(false);
        $project->setSeenByManager(false);
        if($isWithoutTerm){
            $project->setTerm(\DateTime::createFromFormat('Y-m-d', $noTermDefaultValue));
        }
        if($project->getTerm() == $project->getDate()){
            $project->setUrgent(true);
        }
        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }
}
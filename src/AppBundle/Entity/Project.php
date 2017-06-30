<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectRepository")
 */
class Project
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="department", type="string", length=255, nullable=true)
     */
    private $department;

    /**
     * @var string
     *
     * @ORM\Column(name="from_user", type="string", length=255)
     */
    private $fromUser;

    /**
     * @var string
     *
     * @ORM\Column(name="type_task", type="string", length=255, nullable=true)
     */
    private $typeTask;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="term", type="datetime")
     */
    private $term;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_designer", type="datetime", nullable=true)
     */
    private $dateDesigner;

    /**
     * @var bool
     *
     * @ORM\Column(name="designer_accepted", type="boolean", nullable=true)
     */
    private $designerAccepted;
    /**
     * @var bool
     *
     * @ORM\Column(name="for_approval", type="boolean", nullable=true)
     */
    private $forApproval;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_executioner", type="datetime", nullable=true)
     */
    private $dateExecutioner;

    /**
     * @var bool
     *
     * @ORM\Column(name="executioner_accepted", type="boolean", nullable=true)
     */
    private $executionerAccepted;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="over_date", type="datetime", nullable=true)
     */
    private $overDate;

    private $status;

    /**
     * @var bool
     *
     * @ORM\Column(name="working", type="boolean", nullable=true)
     */
    private $working;
    /**
     * @var bool
     *
     * @ORM\Column(name="hold", type="boolean", nullable=true)
     */
    private $hold;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="designer_finished_date", type="datetime", nullable=true)
     */
    private $designerFinishedDate;

    /**
     * @var string
     *
     * @ORM\Column(name="designer", type="string", length=255, nullable=true)
     */
    private $designer;

    /**
     * @var string
     *
     * @ORM\Column(name="Executioner", type="string", length=255, nullable=true)
     */
    private $executioner;

    /**
     * @var bool
     *
     * @ORM\Column(name="isOver", type="boolean")
     */
    private $isOver;

    /**
     * @var bool
     *
     * @ORM\Column(name="seen_by_little_boss", type="boolean", nullable=true)
     */
    private $seenByLittleBoss;

    private $comments;

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }
    /**
     * @var bool
     *
     * @ORM\Column(name="seen_by_designer", type="boolean", nullable=true)
     */
    private $seenByDesigner;

    /**
     * @var bool
     *
     * @ORM\Column(name="seen_by_manager", type="boolean", nullable=true)
     */
    private $seenByManager;

    /**
     * @var bool
     *
     * @ORM\Column(name="seen_by_executioner", type="boolean", nullable=true)
     */
    private $seenByExecutioner;

    /**
     * @var bool
     *
     * @ORM\Column(name="seen_by_boss", type="boolean", nullable=true)
     */
    private $seenByBoss;
    /**
     * @var bool
     *
     * @ORM\Column(name="approved", type="boolean", nullable=true)
     */
    private $approved;

    /**
     * @var bool
     *
     * @ORM\Column(name="urgent", type="boolean", nullable=true)
     */
    private $urgent;
    /**
     * @var string
     *
     */
    private $managerFiles;

    /**
     * @var string
     *
     * @ORM\Column(name="manager_link", type="text", nullable=true)
     */
    private $managerLink;

    /**
     * @var string
     *
     * @ORM\Column(name="designer_link", type="text", nullable=true)
     */
    private $designerLink;
    /**
     * @var ArrayCollection
     *
     */
    private $designerFiles;

    private $class;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Files", mappedBy="project")
     */
    private $files;

    /**
     * @var bool
     *
     * @ORM\Column(name="rejected", type="boolean", nullable=true)
     */
    private $rejected;


    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    /**
     * @return \DateTime
     */
    public function getOverDate()
    {
        return $this->overDate;
    }

    /**
     * @param \DateTime $overDate
     */
    public function setOverDate($overDate)
    {
        $this->overDate = $overDate;
    }

    /**
     * @return bool
     */
    public function isWorking()
    {
        return $this->working;
    }

    /**
     * @param bool $working
     */
    public function setWorking($working)
    {
        $this->working = $working;
    }

    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param mixed $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }


    /**
     * @return bool
     */
    public function isRejected()
    {
        return $this->rejected;
    }

    /**
     * @param bool $rejected
     */
    public function setRejected($rejected)
    {
        $this->rejected = $rejected;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }


    /**
     * @return bool
     */
    public function isSeenByBoss()
    {
        return $this->seenByBoss;
    }

    /**
     * @param bool $seenByBoss
     */
    public function setSeenByBoss($seenByBoss)
    {
        $this->seenByBoss = $seenByBoss;
    }

    /**
     * @return string
     */
    public function getManagerFiles()
    {

        foreach ($this->files as $file){
            /** @var Files $file */
            if($file->getFromUser() == 'Manager'){
                $this->managerFiles[] = $file;
            }
        }
        return $this->managerFiles;
    }

    /**
     * @param string $managerFiles
     */
    public function setManagerFiles($managerFiles)
    {
        $this->managerFiles = $managerFiles;
    }

    /**
     * @return string
     */
    public function getManagerLink()
    {
        return $this->managerLink;
    }

    /**
     * @param string $managerLink
     */
    public function setManagerLink($managerLink)
    {
        $this->managerLink = $managerLink;
    }

    /**
     * @return string
     */
    public function getDesignerLink()
    {
        return $this->designerLink;
    }

    /**
     * @param string $designerLink
     */
    public function setDesignerLink($designerLink)
    {
        $this->designerLink = $designerLink;
    }

    /**
     * @return ArrayCollection
     */
    public function getDesignerFiles()
    {
        foreach ($this->files as $file){
            /** @var Files $file */
            if($file->getFromUser() == 'Designer'){
                $this->managerFiles[] = $file;
            }
        }
        return $this->designerFiles;
    }

    /**
     * @param ArrayCollection $designerFiles
     */
    public function setDesignerFiles($designerFiles)
    {
        $this->designerFiles = $designerFiles;
    }




    /**
     * @return bool
     */
    public function isUrgent()
    {
        return $this->urgent;
    }

    /**
     * @param bool $urgent
     */
    public function setUrgent($urgent)
    {
        $this->urgent = $urgent;
    }



    /**
     * @return bool
     */
    public function isSeenByDesigner()
    {
        return $this->seenByDesigner;
    }

    /**
     * @param bool $seenByDesigner
     */
    public function setSeenByDesigner($seenByDesigner)
    {
        $this->seenByDesigner = $seenByDesigner;
    }

    /**
     * @return bool
     */
    public function isSeenByManager()
    {
        return $this->seenByManager;
    }

    /**
     * @param bool $seenByManager
     */
    public function setSeenByManager($seenByManager)
    {
        $this->seenByManager = $seenByManager;
    }

    /**
     * @return bool
     */
    public function isSeenByExecutioner()
    {
        return $this->seenByExecutioner;
    }

    /**
     * @param bool $seenByExecutioner
     */
    public function setSeenByExecutioner($seenByExecutioner)
    {
        $this->seenByExecutioner = $seenByExecutioner;
    }



    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }
    /**
     * @return bool
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * @param bool $approved
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
    }




    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set department
     *
     * @param string $department
     *
     * @return Project
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return string
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set fromUser
     *
     * @param string $fromUser
     *
     * @return Project
     */
    public function setFromUser($fromUser)
    {
        $this->fromUser = $fromUser;

        return $this;
    }

    /**
     * Get fromUser
     *
     * @return string
     */
    public function getFromUser()
    {
        return $this->fromUser;
    }

    /**
     * Set typeTask
     *
     * @param string $typeTask
     *
     * @return Project
     */
    public function setTypeTask($typeTask)
    {
        $this->typeTask = $typeTask;

        return $this;
    }

    /**
     * Get typeTask
     *
     * @return string
     */
    public function getTypeTask()
    {
        return $this->typeTask;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Project
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set term
     *
     * @param \DateTime $term
     *
     * @return Project
     */
    public function setTerm($term)
    {
        $this->term = $term;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSeenByLittleBoss()
    {
        return $this->seenByLittleBoss;
    }

    /**
     * @param bool $seenByLittleBoss
     */
    public function setSeenByLittleBoss($seenByLittleBoss)
    {
        $this->seenByLittleBoss = $seenByLittleBoss;
    }

    /**
     * Get term
     *
     * @return \DateTime
     */
    public function getTerm()
    {
            return $this->term;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Project
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set dateDesigner
     *
     * @param \DateTime $dateDesigner
     *
     * @return Project
     */
    public function setDateDesigner($dateDesigner)
    {
        $this->dateDesigner = $dateDesigner;

        return $this;
    }

    /**
     * Get dateDesigner
     *
     * @return \DateTime
     */
    public function getDateDesigner()
    {
        return $this->dateDesigner;
    }

    /**
     * Set designerAccepted
     *
     * @param boolean $designerAccepted
     *
     * @return Project
     */
    public function setDesignerAccepted($designerAccepted)
    {
        $this->designerAccepted = $designerAccepted;

        return $this;
    }

    /**
     * Get designerAccepted
     *
     * @return bool
     */
    public function getDesignerAccepted()
    {
        return $this->designerAccepted;
    }

    /**
     * Set dateExecutioner
     *
     * @param \DateTime $dateExecutioner
     *
     * @return Project
     */
    public function setDateExecutioner($dateExecutioner)
    {
        $this->dateExecutioner = $dateExecutioner;

        return $this;
    }

    /**
     * Get dateExecutioner
     *
     * @return \DateTime
     */
    public function getDateExecutioner()
    {
        return $this->dateExecutioner;
    }

    /**
     * Set executionerAccepted
     *
     * @param boolean $executionerAccepted
     *
     * @return Project
     */
    public function setExecutionerAccepted($executionerAccepted)
    {
        $this->executionerAccepted = $executionerAccepted;

        return $this;
    }

    /**
     * Get executionerAccepted
     *
     * @return bool
     */
    public function getExecutionerAccepted()
    {
        return $this->executionerAccepted;
    }

    /**
     * Set designerFinishedDate
     *
     * @param \DateTime $designerFinishedDate
     *
     * @return Project
     */
    public function setDesignerFinishedDate($designerFinishedDate)
    {
        $this->designerFinishedDate = $designerFinishedDate;

        return $this;
    }

    /**
     * Get designerFinishedDate
     *
     * @return \DateTime
     */
    public function getDesignerFinishedDate()
    {
        return $this->designerFinishedDate;
    }

    /**
     * Set designer
     *
     * @param string $designer
     *
     * @return Project
     */
    public function setDesigner($designer)
    {
        $this->designer = $designer;

        return $this;
    }

    /**
     * Get designer
     *
     * @return string
     */
    public function getDesigner()
    {
        return $this->designer;
    }

    /**
     * Set executioner
     *
     * @param string $executioner
     *
     * @return Project
     */
    public function setExecutioner($executioner)
    {
        $this->executioner = $executioner;

        return $this;
    }

    /**
     * Get executioner
     *
     * @return string
     */
    public function getExecutioner()
    {
        return $this->executioner;
    }

    /**
     * Set isOver
     *
     * @param boolean $isOver
     *
     * @return Project
     */
    public function setIsOver($isOver)
    {
        $this->isOver = $isOver;

        return $this;
    }

    /**
     * Get isOver
     *
     * @return bool
     */
    public function getIsOver()
    {
        return $this->isOver;
    }

    /**
     * @return bool
     */
    public function isHold()
    {
        return $this->hold;
    }

    /**
     * @param bool $hold
     */
    public function setHold($hold)
    {
        $this->hold = $hold;
    }
    /**
     * @return bool
     */
    public function isForApproval()
    {
        return $this->forApproval;
    }

    /**
     * @param bool $forApproval
     */
    public function setForApproval($forApproval)
    {
        $this->forApproval = $forApproval;
    }


}


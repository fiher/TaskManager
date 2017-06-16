<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Zadanie
 *
 * @ORM\Table(name="zadanie")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ZadanieRepository")
 */
class Zadanie
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
     * @ORM\Column(name="fromUser", type="string", length=255)
     */
    private $fromUser;

    /**
     * @var string
     *
     * @ORM\Column(name="typeTask", type="string", length=255, nullable=true)
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
     * @ORM\Column(name="dateDesigner", type="datetime", nullable=true)
     */
    private $dateDesigner;

    /**
     * @var bool
     *
     * @ORM\Column(name="designerAccepted", type="boolean", nullable=true)
     */
    private $designerAccepted;
    /**
     * @var bool
     *
     * @ORM\Column(name="forApproval", type="boolean", nullable=true)
     */
    private $forApproval;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateExecutioner", type="datetime", nullable=true)
     */
    private $dateExecutioner;

    /**
     * @var bool
     *
     * @ORM\Column(name="executionerAccepted", type="boolean", nullable=true)
     */
    private $executionerAccepted;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="overDate", type="datetime", nullable=true)
     */
    private $overDate;

    private $status;

    /**
     * @var bool
     *
     * @ORM\Column(name="hold", type="boolean", nullable=true)
     */
    private $hold;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="designerFinishedDate", type="datetime", nullable=true)
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
     * @ORM\Column(name="seenByLittleBoss", type="boolean", nullable=true)
     */
    private $seenByLittleBoss;

    /**
     * @var bool
     *
     * @ORM\Column(name="seenByDesigner", type="boolean", nullable=true)
     */
    private $seenByDesigner;

    /**
     * @var bool
     *
     * @ORM\Column(name="seenByManager", type="boolean", nullable=true)
     */
    private $seenByManager;

    /**
     * @var bool
     *
     * @ORM\Column(name="seenByExecutioner", type="boolean", nullable=true)
     */
    private $seenByExecutioner;

    /**
     * @var bool
     *
     * @ORM\Column(name="seenByBoss", type="boolean", nullable=true)
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
     * @ORM\Column(name="ergent", type="boolean", nullable=true)
     */
    private $ergent;
    /**
     * @var string
     *
     * @ORM\Column(name="file", type="text", nullable=true)
     */
    private $file;


    private $class;


    /**
     * @var bool
     *
     * @ORM\Column(name="rejected", type="boolean", nullable=true)
     */
    private $rejected;

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
     * @return bool
     */
    public function isErgent()
    {
        return $this->ergent;
    }

    /**
     * @param bool $ergent
     */
    public function setErgent($ergent)
    {
        $this->ergent = $ergent;
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
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
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
     * @return Zadanie
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
     * @return Zadanie
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
     * @return Zadanie
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
     * @return Zadanie
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
     * @return Zadanie
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
     * @return Zadanie
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
     * @return Zadanie
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
     * @return Zadanie
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
     * @return Zadanie
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
     * @return Zadanie
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
     * @return Zadanie
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
     * @return Zadanie
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
     * @return Zadanie
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
     * @return Zadanie
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


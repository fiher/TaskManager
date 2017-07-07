<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Files
 *
 * @ORM\Table(name="files")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FilesRepository")
 */
class Files
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    /**
     * @var string
     *
     * @ORM\Column(name="from_user", type="string", nullable=true)
     */
    private $fromUser;
    /**
     * @var bool
     *
     * @ORM\Column(name="rejected", type="boolean", nullable=true)
     */
    private $rejected;
    /**
     * @var string
     *
     * @ORM\Column(name="file_path", type="text")
     */
    private $filePath;

    /**
     * @var string
     *
     * @ORM\Column(name="file_extension", type="text")
     */
    private $fileExtension;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project", inversedBy="files")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;
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
     * Set filePath
     *
     * @param string $filePath
     *
     * @return Files
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Get filePath
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @return string
     */
    public function getFromUser(): string
    {
        return $this->fromUser;
    }

    /**
     * @param string $fromUser
     */
    public function setFromUser(string $fromUser)
    {
        $this->fromUser = $fromUser;
    }



    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param mixed $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }


    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
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
     * @return string
     */
    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }

    /**
     * @param string $fileExtension
     */
    public function setFileExtension(string $fileExtension)
    {
        $this->fileExtension = $fileExtension;
    }



}


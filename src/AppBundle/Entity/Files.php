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
     * @var string
     *
     * @ORM\Column(name="filePath", type="text")
     */
    private $filePath;

    /**
     * @var int
     *
     * @ORM\Column(name="zadanieID", type="integer")
     */
    private $zadanieID;


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
     * Set zadanieID
     *
     * @param integer $zadanieID
     *
     * @return Files
     */
    public function setZadanieID($zadanieID)
    {
        $this->zadanieID = $zadanieID;

        return $this;
    }

    /**
     * Get zadanieID
     *
     * @return int
     */
    public function getZadanieID()
    {
        return $this->zadanieID;
    }
}


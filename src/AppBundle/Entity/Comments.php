<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comments
 *
 * @ORM\Table(name="comments")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommentsRepository")
 */
class Comments
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
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="madeBy", type="string", length=255)
     */
    private $madeBy;

    /**
     * @var string
     *
     * @ORM\Column(name="creatorRole", type="string", length=255)
     */
    private $creatorRole;

    /**
     * @var int
     *
     * @ORM\Column(name="zadanieID", type="integer")
     */
    private $zadanieID;

    private $class;

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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Comments
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }
    /**
     * @var string
     *
     * @ORM\Column(name="toUser", type="text", nullable=true)
     */

    private $toUser;
    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date->format('Y-m-d H:i:s');
    }

    /**
     * @return string
     */
    public function getToUser()
    {
        return $this->toUser;
    }

    /**
     * @param string $toUser
     */
    public function setToUser($toUser)
    {
        $this->toUser = $toUser;
    }



    /**
     * Set content
     *
     * @param string $content
     *
     * @return Comments
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set madeBy
     *
     * @param string $madeBy
     *
     * @return Comments
     */
    public function setMadeBy($madeBy)
    {
        $this->madeBy = $madeBy;

        return $this;
    }

    /**
     * Get madeBy
     *
     * @return string
     */
    public function getMadeBy()
    {
        return $this->madeBy;
    }

    /**
     * Set creatorRole
     *
     * @param string $creatorRole
     *
     * @return Comments
     */
    public function setCreatorRole($creatorRole)
    {
        $this->creatorRole = $creatorRole;

        return $this;
    }

    /**
     * Get creatorRole
     *
     * @return string
     */
    public function getCreatorRole()
    {
        return $this->creatorRole;
    }

    /**
     * Set zadanieID
     *
     * @param integer $zadanieID
     *
     * @return Comments
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


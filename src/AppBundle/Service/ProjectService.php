<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 21-Jun-17
 * Time: 11:50
 */

namespace AppBundle\Service;


use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class ProjectService
{
    private $entityManager;
    private $session;
    private $manager;
    public function __construct(
        EntityManagerInterface $entityManager,
        Session $session,
        ManagerRegistry $manager)
    {
        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->manager = $manager;
    }
    public function newComment(){

    }
}
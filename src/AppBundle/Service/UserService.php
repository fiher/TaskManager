<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 29-Jun-17
 * Time: 12:09
 */

namespace AppBundle\Service;


use AppBundle\Entity\Files;
use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\Session;

class UserService
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
        $this->userRepository = $this->manager->getRepository('AppBundle:User');
    }
    public function getUserByUsername(string $username) {
        return $this->userRepository->loadUserByUsername($username);
    }

}
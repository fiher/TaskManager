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
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\Session;

class FilesService
{
    private $entityManager;
    private $session;
    private $manager;
    private $targetDirectory;
    public function __construct(
        EntityManagerInterface $entityManager,
        Session $session,
        ManagerRegistry $manager,CommentsService $commentsService)
    {
        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->manager = $manager;

    }
    public function uploadFileAndReturnName(UploadedFile $file,$targetDirectory){
        $this->targetDirectory = $targetDirectory;
        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        $file->move(
            $targetDirectory,
            $fileName);
        return $fileName;
    }
    public function createFile($fileName,$project,User $user){
        $file = new Files();
        $file->setRejected(false);
        $file->setFilePath($this->targetDirectory."/".$fileName);
        $file->setFromUser($user->getType());
        $file->setProject($project);
        $file->setDate(new \DateTime());
        $this->entityManager->persist($file);
        $this->entityManager->flush();
    }

}
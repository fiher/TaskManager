<?php

namespace AppBundle\Service;


use AppBundle\Entity\Files;
use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\Session;

class FilesService
{
    private $entityManager;
    private $session;
    private $manager;
    private $targetDirectory;
    private $filesRepository;
    public function __construct(
        EntityManagerInterface $entityManager,
        Session $session,
        ManagerRegistry $manager)
    {
        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->manager = $manager;
        $this->filesRepository = $this->manager->getRepository('AppBundle:Files');

    }
    public function uploadFileAndReturnName(UploadedFile $file,$targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        $file->move(
            $targetDirectory,
            $fileName);
        return $fileName;
    }
    public function createFile($fileName,$project,User $user,$fileExtension)
    {
        $file = new Files();
        $file->setRejected(false);
        $file->setFilePath(str_replace('/home/winb0maq/','http://',"http://img.winbet-bg.com/files/".$fileName));
        $file->setFromUser($user->getType());
        $file->setProject($project);
        $file->setDate(new \DateTime());
        $file->setFileExtension(explode('.',$fileName)[1]);
        $this->entityManager->persist($file);
        $this->entityManager->flush();
    }
    public function rejectFile($fileID)
    {
        $file = $this->filesRepository->find($fileID);
        $file->setRejected(true);
        $this->entityManager->persist($file);
        $this->entityManager->flush();
    }
    public function deleteFile(Files $file)
    {
        $fileSystem = new Filesystem();
        $fileSystem->remove($file->getFilePath());
        $this->entityManager->remove($file);
        $this->entityManager->flush();
    }
}
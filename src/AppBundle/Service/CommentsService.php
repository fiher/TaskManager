<?php

/**
 * Created by PhpStorm.
 * User: PC
 * Date: 21-Jun-17
 * Time: 11:40
 */
namespace AppBundle\Service;


use AppBundle\Entity\Comments;
use AppBundle\Entity\User;
use AppBundle\Repository\CommentsRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class CommentsService
{
    private $entityManager;
    private $session;
    private $manager;
    private $commentsRepository;
    public function __construct(
        EntityManagerInterface $entityManager,
        Session $session,
        ManagerRegistry $manager,CommentsRepository $commentsRepository)
    {
        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->manager = $manager;
        $this->commentsRepository = $commentsRepository;
    }
    public function newComment(Comments $comment, User $user, \DateTime $date){
        $comment->setMadeBy($user->getFullName());
        $comment->setCreatorRole($user->getType());
        $comment->setDate(new \DateTime());
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }
    function filterComments($comments,User $user){
        /**
         * @var $comment Comments
         * @var $user User
         */
        if($user->getType() != "LittleBoss") {
            for ($i = 0; $i < count($comments); $i++) {
                $comment = $comments[$i];
                if ($comment->getCreatorRole() == "LittleBoss") {
                    if (!in_array($comment->getToUser(), explode(" ", $user->getRole()))) {
                        unset($comments[$i]);
                        $i--;
                        $comments = array_values($comments);
                    }
                } else {
                    if ($comment->getCreatorRole() == 'Manager' && $user->getUsername() == 'm.stanev'){
                        continue;
                    }
                    else if ($comment->getCreatorRole() != $user->getType()) {
                        unset($comments[$i]);
                        $i--;
                        $comments = array_values($comments);
                    }
                }
            }
        }
        foreach ($comments as $comment){
            $comment->setClass($comment->getCreatorRole());
        }
        return $comments;
    }
    public function findCommentsByProjectID(int $id) {
        return $this->commentsRepository->findByProjectID($id);
    }
}
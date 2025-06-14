<?php

namespace App\Controller\admin;

use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminCommentController extends AbstractController{

    #[Route('/admin/comments', name:'admin-display-comments')]
    #[IsGranted('ROLE_ADMIN')]

    public function displayComments(CommentRepository $commentRepository){

        $comments = $commentRepository->findAll();
        return $this->render('admin/comments/displayComments.html.twig', [
            'comments' => $comments,
        ]);
    }
}
<?php

namespace App\Controller\admin;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminCommentController extends AbstractController{

    // AFFICHER TOUS LES COMMENTAIRES 
    #[Route('/admin/comments', name:'admin-display-comments')]
    #[IsGranted('ROLE_ADMIN')]

    public function displayComments(CommentRepository $commentRepository){

        $comments = $commentRepository->findAll();
        return $this->render('admin/comments/displayComments.html.twig', [
            'comments' => $comments,
        ]);
    }

// -----------------------------------------------------------------------------------------------------------------//

    // SUPPRESSION DE COMMENTAIRE
    #[Route('/admin/comments/delete/{id}', name:'admin-delete-comment')]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteComment(Comment $comment, EntityManagerInterface $entityManager){

        $entityManager->remove($comment);
        $entityManager->flush();

        $this->addFlash('success', 'Commentaire supprimé.');
        return $this->redirectToRoute('admin-display-comments');
    }
}
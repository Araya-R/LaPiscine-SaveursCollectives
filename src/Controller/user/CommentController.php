<?php

namespace App\Controller\user;

use App\Entity\Comment;
use App\Entity\Recipe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class CommentController extends AbstractController
{

    #[Route('/recipe/{id}/comment', name: 'recipe-add-comment', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function addComment(Request $request, Recipe $recipe, EntityManagerInterface $entityManager)
    {

        $content = trim($request->request->get('comment'));

        if (empty($content)) {
            $this->addFlash('error', 'Le commentaire ne peut être vide.');
            return $this->redirectToRoute('user-detail-recipe', ['id' => $recipe->getId()]);
        }

        $comment = new Comment();
        $comment->setContent($content);
        $comment->setCreatedAt(new \DateTimeImmutable());
        $comment->setUser($this->getUser());
        $comment->setRecipe($recipe);

        $entityManager->persist($comment);
        $entityManager->flush();

        $this->addFlash('success', 'commentaire ajouté');

        return $this->redirectToRoute('user-detail-recipe', ['id' => $recipe->getId()]);
    }
}

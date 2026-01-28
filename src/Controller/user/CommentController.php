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
    // La route : définit URL de la page/ nom interne de la route/ cette route n'accepte que les requêtes POST (important pour la sécurité = soumission de formulaire)
    #[Route('/recipe/{id}/comment', name: 'recipe-add-comment', methods: ['POST'])]
    
    // Sécurité: restriction par rôle. Ici, seuls les utilisateurs CONNECTÉ avec ROLE_USER peuvent accéder à cette action
    #[IsGranted('ROLE_USER')]

    // Injection automatique (autowiring)
    // Symfony injecte automatiquement Request $request (les données HTTP :POST/GET)
    // EntityManager = sauvegarder les entités en base de données
    public function addComment(Request $request, Recipe $recipe, EntityManagerInterface $entityManager)
    {
        //Je récupère le contenu du commentaire
        //On supprime les espaces avant/après 
        $content = trim($request->request->get('comment'));

        // Ensuite on vérifie que le commentaire n'est pas vide
        // En cas d'erreur: affichage d'un message et rediction vers une autre page
        if (empty($content)) {
            $this->addFlash('error', 'Le commentaire ne peut être vide.');
            return $this->redirectToRoute('user-detail-recipe', ['id' => $recipe->getId()]);
        }

        //Création du commentaire
        // Créer une nouvelle entité Comment
        $comment = new Comment();

        // remplire des propriétés du commentaire
        // Le texte
        $comment->setContent($content);
        // La date de création
        $comment->setCreatedAt(new \DateTimeImmutable());
        // on associe le commentaire à l'utilisateur connecté
        $comment->setUser($this->getUser());
        // lier avec la recette concernée
        $comment->setRecipe($recipe);

        // Sauvegarde en base de données
        $entityManager->persist($comment); 
        $entityManager->flush();

        // affichage message de succès
        $this->addFlash('success', 'commentaire ajouté');

        // redirection finale 
        return $this->redirectToRoute('user-detail-recipe', ['id' => $recipe->getId()]);
    }
}

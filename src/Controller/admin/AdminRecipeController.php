<?php

namespace App\Controller\admin;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminRecipeController extends AbstractController{

    #[Route('/admin/recipes', name:'admin-display-recipes')]
    #[IsGranted('ROLE_ADMIN')]
    public function displayRecipes(RecipeRepository $recipeRepository){
        
        $recipes = $recipeRepository->findAll();
        return $this->render('admin/recipes/displayRecipes.html.twig', [
            'recipes' => $recipes,
            
        ]);
    }

// -----------------------------------------------------------------------------------------------------------------//

    #[Route('/admin/recipes/{id}', name:'admin-show-recipe')]
    #[IsGranted('ROLE_ADMIN')]

    public function showRecipe( int $id, RecipeRepository $recipeRepository){
        $recipe = $recipeRepository->find($id);
        if (!$recipe) {
            throw $this->createNotFoundException('Recipe not found');
        }
        return $this->render('admin/recipes/detailRecipe.html.twig', [
            'recipe' => $recipe
        ]);
    }

// -----------------------------------------------------------------------------------------------------------------//
    #[Route('admin/recipes/delete/{id}', name:'admin-delete-recipe')]
    #[IsGranted('ROLE_ADMIN')]

    public function deleteRecipe(Recipe $recipe, EntityManagerInterface $entityManager){
        
        //Symfony va automatiquement chercher l'id de la recette =>
        //comme si on fait $recipe = $recipeRepository->find($id);

        //on supprime les commentaires liés à la recette
        //Afin d'éviter les erreurs de contrainte d'intégrité référentielle (foreign key)
        foreach($recipe->getComments() as $comment){
            $entityManager->remove($comment);
        }
        
        //on supprime les likes liés à la recette
        foreach ($recipe->getLikes() as $like){
            $entityManager->remove($like);
        }

        $entityManager->remove($recipe);
        $entityManager->flush();

        $this->addFlash('success', 'Recette supprimée avec succès !');
        return $this->redirectToRoute('admin-display-recipes');
    }
}
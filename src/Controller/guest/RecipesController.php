<?php 

namespace App\Controller\guest;

use App\Repository\CommentRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipesController extends AbstractController{

    #[Route('/detail-recipe/{id}', name:'detail-recipe')]
    public function displayRecipe($id, RecipeRepository $recipeRepository,CommentRepository $commentRepository){
        
        $recipe=$recipeRepository->findOneById($id);
        if(!$recipe){
            $this->addFlash('error', 'Recette non trouvée');
            return $this->redirectToRoute('404');
        }

        $comments=$commentRepository->findByRecipeOrderedByDate($recipe);
        return $this->render('guest/recipes/detailRecipe.html.twig', ['recipe'=>$recipe, 'comments'=>$comments]);
    }
    
//-----------------------------------------------------------------------------------------------//

    #[Route('/search-recipe', name:'search-recipe')]
    public function search(Request $request, RecipeRepository $recipeRepository){
        
        $search=$request->query->get('search');
        if(!$search){
            $this->addFlash('error', 'Veuillez entrer un mot clé.');
            return $this->redirectToRoute('guest-home');
        }

        $recipesFound =$recipeRepository->findBySearch($search);
        if(empty($recipesFound)){
            $this->addFlash('error', 'Aucune recette trouvée.');
            return $this->redirectToRoute('404');
        }
        return $this->render('guest/recipes/searchResults.html.twig', [
            'recipesFound'=>$recipesFound,
            'search'=>$search
        ]);
    }

    
}

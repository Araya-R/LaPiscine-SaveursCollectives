<?php 

namespace App\Controller\user;

use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserPageController extends AbstractController{

    #[Route('/user', name:'user-home')]
    public function homePage(CategoryRepository $categoryRepository, RecipeRepository $recipeRepository): Response
    {
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('login');
        }
        //on récupère toutes les catégories
        $categories=$categoryRepository->findAll();

        //on crée un tableau pour stocker les résultats
        $categoriesWithRecipes = [];

        //on boucle sur chaque catégorie récupérée
        foreach ($categories as $category){

            //on fait appel à la méthode créée avec en param id d'une catégorie 
            //pour extraire id on utilise la méthode getter de la classe Category
            $recipes=$recipeRepository->findTopPublishedByCategory($category->getId());

            //on prépare une structure contenant la catégorie et ses recettes
            $categoriesWithRecipes[]=[
                'category'=>$category,
                'recipes'=>$recipes,
            ];
        }

        $mostLiked=$recipeRepository->findMostLiked(5);

        return $this->render('user/home.html.twig', [
            'categoriesWithRecipes' =>$categoriesWithRecipes,
            'mostLiked'=> $mostLiked,
        
        ]);
    }

//-----------------------------------------------------------------------------------------------//

    #[Route('/user/about', name:'user-about')]
    public function about(): Response{

        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('login');
        }
        return $this->render('user/about.html.twig');
    }

//-----------------------------------------------------------------------------------------------//

    #[Route('/user/404', name:'user-404')]
    public function DisplayPage404(){

        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('login');
        }
        return $this->render('user/404.html.twig');
    }
}
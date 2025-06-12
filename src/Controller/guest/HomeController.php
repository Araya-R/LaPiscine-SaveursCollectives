<?php

namespace App\Controller\guest;

use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    #[Route('/', name: 'guest-home')]
    public function homePage(CategoryRepository $categoryRepository, RecipeRepository $recipeRepository): Response
    {
        //on récupère toutes les catégories
        $categories=$categoryRepository->findAll();

        //on crée un tableau pour stocker les résultats
        $categoriesWithRecipes = [];

        //on boucle sur chaque catégorie récupérée
        foreach ($categories as $category){

            //on fait appel à la méthode créée, avec en param id d'une catégorie 
            //pour extraire id on utilise la méthode getter de la classe Category
            $recipes=$recipeRepository->findTopPublishedByCategory($category->getId());

            //on prépare une structure contenant la catégorie et ses recettes
            $categoriesWithRecipes[]=[
                'category'=>$category,
                'recipes'=>$recipes,
            ];
        }

        $mostLiked=$recipeRepository->findMostLiked(5);

        return $this->render('guest/home.html.twig', [
            'categoriesWithRecipes' =>$categoriesWithRecipes,
            'mostLiked'=> $mostLiked,
        
        ]);
    }

    
}

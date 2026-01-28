<?php 

namespace App\Controller\user;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserCategoryController extends AbstractController{

    //La route : URL / nom / méthode par défaut GET
    #[Route('/user/categories', name:'user-categories')]

    // Créer une méthode afin d'afficher la liste des catégories
    // autowiring : injecte auto le repository lié à l'entité Category/Sert à interagir avec la base de données
    public function displayCategories(CategoryRepository $categoryRepository){
       
        //Récupérer toutes les catégories de la base/ retourne un tableau d'objet 
        $categories=$categoryRepository->findAll();

        // afficher avec Twig
        return $this->render('user/categories/listCategories.html.twig', ['categories'=>$categories]);
    }


    // affiche une catégorie précise et ses recettes associées
    #[Route('/user/category/{id}', name:'user-show-category')]
    public function showByCategory(Category $category){
        
        // récupérer des recettes liées 
        $recipes=$category->getRecipes();

        // affichage/ envoyer à Twig la catégorie sélectionnée et la liste de ses recettes
        return $this->render('user/categories/show.html.twig',['category'=>$category,'recipes'=>$recipes]);
    }
}
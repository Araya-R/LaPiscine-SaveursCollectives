<?php 

namespace App\Controller\guest;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController{

    #[Route('/categories', name:'categories')]
    public function displayCategories(CategoryRepository $categoryRepository){
        $categories=$categoryRepository->findAll();
        return $this->render('guest/categories/listCategories.html.twig', ['categories'=>$categories]);
    }

    #[Route('/category/{id}', name:'show-category')]
    public function showByCategory(Category $category){
        $recipes=$category->getRecipes();

        return $this->render('guest/categories/show.html.twig',['category'=>$category,'recipes'=>$recipes]);
    }
}
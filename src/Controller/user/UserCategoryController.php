<?php 

namespace App\Controller\user;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserCategoryController extends AbstractController{

    #[Route('/user/categories', name:'user-categories')]
    public function displayCategories(CategoryRepository $categoryRepository){
        $categories=$categoryRepository->findAll();
        return $this->render('user/categories/listCategories.html.twig', ['categories'=>$categories]);
    }

    #[Route('/user/category/{id}', name:'user-show-category')]
    public function showByCategory(Category $category){
        $recipes=$category->getRecipes();

        return $this->render('user/categories/show.html.twig',['category'=>$category,'recipes'=>$recipes]);
    }
}
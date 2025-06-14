<?php

namespace App\Controller\admin;

use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminPageContoller extends AbstractController{

    #[Route('/admin', name: 'admin-home')]
    #[IsGranted('ROLE_ADMIN')]
    public function homePage(RecipeRepository $recipeRepository, UserRepository $userRepository){

        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez vous connecter pour accéder à cette page.');
        }

        $totalRecipes= $recipeRepository->count([]);
        $totalUsers = $userRepository->count([]);
        $publishedRecipes = $recipeRepository->countPublishedRecipes();
        $unpublishedRecipes = $recipeRepository->countUnpublishedRecipes();
        $mostLikedRecipes = $recipeRepository->findMostLiked(3);
        $mostCommentedRecipes = $recipeRepository->findMostCommented(3);
       

        return $this->render('admin/home.html.twig', [
            'user' => $user,
            'totalRecipes' => $totalRecipes,
            'totalUsers' => $totalUsers,
            'publishedRecipes' => $publishedRecipes,
            'unpublishedRecipes' => $unpublishedRecipes,
            'mostLikedRecipes' => $mostLikedRecipes,
            'mostCommentedRecipes' => $mostCommentedRecipes,
        ]
        );
    }
}
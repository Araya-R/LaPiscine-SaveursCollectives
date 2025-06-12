<?php

namespace App\Controller\user;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Recipe;
use PHPUnit\Framework\MockObject\Rule\Parameters;
use Symfony\Bundle\SecurityBundle\Security\UserAuthenticator;
use Symfony\Component\Routing\Attribute\Route as AttributeRoute;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserRecipeController extends AbstractController{

    //CRÉATION D'UNE NOUVELLE RECETTE

    // définir une route Symfony (url & redirections/liens)
    #[Route('/user/recipe/new', name:'recipe-new')]
    #[IsGranted('ROLE_USER')]
    
    //Injection de dépendance
    //Symfony injecte automatiquement 
    //Request: accéder aux données de la requête//formulaire
    //EntityManagerInterface: enregistrer ou mettre à jour les entités en base de données
    //ParameterBagInterface: pour accéder aux parametres du projet// chemin du dossier uploads
    //CategoryRepositories: rechercher les catégories
    public function newRecipe(Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag, CategoryRepository $categoryRepository){

        if($request->isMethod('POST')){ // on vérifie que l'user a soumis le formulaire

            //Récupération des données du formulaire
            $title=$request->request->get('title');
            $description=$request->request->get('description');
            $servings=$request->request->get('servings');
            $prepTime=$request->request->get('prep_time');
            $cookingTime=$request->request->get('cooking_time');

            $ingredients=$request->request->all('ingredients'); //les données en tableau
            $steps=$request->request->all('steps');             // les données en tableau

            $isPublished=$request->request->getBoolean('isPublished');

            //Vérification et chargement de la catégorie
            $categoryId=$request->request->get('category');
            if (empty($categoryId)|| !is_numeric($categoryId)){ //vérifie que l'id de la catégorie est présent & nombre
                $this->addFlash('error', 'Veuillez sélectionner une catégorie valide.');
                return $this->redirectToRoute('recipe-new');
            }
            $category=$categoryRepository->find($categoryId); //récupère l'objet Category correspondant

            if(!$category){ //si elle est introuvable, on redirige vers le formulaire
                $this->addFlash('error', 'Catégorie invalide');
                return $this->redirectToRoute('recipe-new');
            }

            //Création d'un objet avec toutes ses propriétés
            $recipe = new Recipe();

            $recipe->setTitle($title);
            $recipe->setDescription($description);
            $recipe->setServings($servings);
            $recipe->setPrepTime($prepTime);
            $recipe->setCookingTime($cookingTime);

            $recipe->setIngredients(array_values($ingredients));
            $recipe->setSteps(array_values($steps));

            $recipe->setIsPublished($isPublished);
            $recipe->setCategory($category);

            $recipe->setCreatedAt(new \DateTimeImmutable());
            $recipe->setUpdatedAt(new \DateTimeImmutable());

            
            //traitement de l'image (si présente)
            $imageRecipe =$request->files->get('image');

            if($imageRecipe){ //si image présente
                $imageRecipeName=uniqid() . '.' . $imageRecipe->guessExtension(); //gère un nom unique
                $imageRecipe->move($parameterBag->get(name: 'kernel.project_dir') . '/public/uploads/recipes', $imageRecipeName); //déplace l'image 
                $recipe->setImage($imageRecipeName); //stock le nom dans l'entité Recipe
            }

            //Sauvegarde en base de données
            $entityManager->persist($recipe); //prépare l'objet à être enregistré
            $entityManager->flush(); // exécute la requête 

            //message et redirection
            $this->addFlash('success', 'Recette créée avec succès !');
            return $this->redirectToRoute('user-list-recipes');

        }
        //récupère toutes les catégories pour le  select
        $categories=$categoryRepository->findAll();
        return $this->render('user/recipes/createRecipe.html.twig', ['categories' =>$categories]);
    }
    //-----------------------------------------------------------------------------------------------//

    //LISTE DES RECETTES DE L'USER

    #[Route('/user/list-recipes', name:'user-list-recipes')]
    public function displayRecipes (RecipeRepository $recipeRepository){

        $user=$this->getUser();
        if(!$user){
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos recettes.');
        }
        $recipes=$recipeRepository->findBy(['user' =>$user]);
        return $this->render('user/recipes/listRecipes.html.twig',['recipes'=>$recipes]);
    }

    //-----------------------------------------------------------------------------------------------//

    //SUPPRIMER UNE RECETTE

    #[Route('/user/delete-recipe/{id}', name:'user-delete-recipe')]
    public function deleteRecipe($id, RecipeRepository $recipeRepository, EntityManagerInterface $entityManager){
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('login');
        }

        try{
            $recipe = $recipeRepository->findOneById($id);

            if(!$recipe){
                return $this->render('user/404.html.twig', ['message'=>"Recette avec l'Id $id est introuvable."]);
            }

            $entityManager->remove($recipe);
            $entityManager->flush();

            $this->addFlash('success', 'Recette supprimée');
        }catch(\exception $e){
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression de la recette.');
        }
        return $this->redirectToRoute('user-list-recipes');
    }
    //-----------------------------------------------------------------------------------------------//

    //MODIFIER UNE RECETTE

    #[Route('/user/update-recipe/{id}', name:'user-update-recipe')]
    public function updateRecipe($id, RecipeRepository $recipeRepository,CategoryRepository $categoryRepository ,EntityManagerInterface $entityManager, Request $request, ParameterBagInterface $parameterBag){

        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('login');
        }

        $recipe= $recipeRepository->find($id);

        if (!$recipe){
            throw $this->createNotFoundException("La recette avec l'id $id est introuvable");
        }

        $categories=$categoryRepository->findAll();
        
        if($request->isMethod('POST')){

            $user=$this->getUser(); //on récupère l'user courant connecté via getUser()
            if(!$user){             //si la personne n'est connecté, on empêche l'action avec une exception d'accès interdit
                throw $this->createAccessDeniedException("Vous devez être connecté pour créer une recette");
            }

            $title=$request->request->get('title');
            $description=$request->request->get('description');
            $servings=$request->request->get('servings');
            $prepTime=$request->request->get('prep_time');
            $cookingTime=$request->request->get('cooking_time');

            $ingredients=$request->request->all('ingredients');
            $steps=$request->request->all('steps');

            $isPublished=$request->request->getBoolean('isPublished');

            $categoryId= $request->request->get('category');
            $category= $categoryRepository->find($categoryId);

            $imageRecipe =$request->files->get('image');
            $imageRecipeName=null;
            if($imageRecipe){
                $imageRecipeName=uniqid() . '.' . $imageRecipe->guessExtension();
                $imageRecipe->move($parameterBag->get(name: 'kernel.project_dir') . '/public/uploads/recipes', $imageRecipeName);
               
            }

            if(!$category){
                $this->addFlash('error', 'Catégorie invalide');
            }else try{

                $recipe->update($title, $servings, $prepTime, $cookingTime, $description, $ingredients, $steps, $imageRecipeName, $isPublished, $category, $user);

                $entityManager->flush();



                $this->addFlash('success', 'Recette mis à jour avec succès !');

                return $this->redirectToRoute('user-list-recipes');
            }catch(\Exception $e){
                $this->addFlash('error', 'Erreur:' . $e->getMessage());
            }
        }
        return $this->render('user/recipes/updateRecipe.html.twig', ['categories'=>$categories, 'recipe'=>$recipe]);
    }

    //-----------------------------------------------------------------------------------------------//
        #[Route('/user/detail-recipe/{id}', name:'user-detail-recipe')]
    public function displayRecipe($id, RecipeRepository $recipeRepository, CommentRepository $commentRepository){
        
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('login');
        }

        $recipe=$recipeRepository->findOneById($id);
        if(!$recipe){
            $this->addFlash('error', 'Recette non trouvée');
            return $this->redirectToRoute('404');
        }
        $comments = $commentRepository->findByRecipeOrderedByDate($recipe);
        return $this->render('user/recipes/detailRecipe.html.twig', ['recipe'=>$recipe, 'comments'=> $comments]);
    }

    //-----------------------------------------------------------------------------------------------//

    #[Route('/user/search-recipe', name:'user-search-recipe')]
    public function search(Request $request, RecipeRepository $recipeRepository){
        
        $search=$request->query->get('search');
        if(!$search){
            $this->addFlash('error', 'Veuillez entrer un mot clé.');
            return $this->redirectToRoute('user-home');
        }

        $recipesFound =$recipeRepository->findBySearch($search);
        if(empty($recipesFound)){
            $this->addFlash('error', 'Aucune recette trouvée.');
            return $this->redirectToRoute('404');
        }
        return $this->render('user/recipes/searchResults.html.twig', [
            'recipesFound'=>$recipesFound,
            'search'=>$search
        ]);
    }
}   



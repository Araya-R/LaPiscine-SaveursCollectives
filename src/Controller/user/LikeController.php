<?php

namespace App\Controller\user;

use App\Entity\Like;
use App\Entity\Recipe;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LikeController extends AbstractController{
    
    #[Route('/like/toggle/{id}', name:'like-toggle')]
    #[IsGranted ('ROLE_USER')]
    public function toggle(Recipe $recipe, EntityManagerInterface $entityManager, LikeRepository $likeRepository){

        //on récupère l'user connecté
        $user= $this->getUser();


        //on cherche en base s'il existe déjà un like = à cet user ET cette recette
        $existingLike = $likeRepository->findOneBy(['user'=>$user, 'recipe'=>$recipe]);

        if($existingLike){ //si oui alors on le supprime 
            $entityManager->remove($existingLike);

        }else {           //si non on crée un nouveau like
            $like=new Like();
            $like->setUser($user);
            $like->setRecipe($recipe);
            
            $entityManager->persist($like);
        }

        $entityManager->flush();
        return $this->redirectToRoute('user-detail-recipe', ['id'=>$recipe->getId()]);
    }
}
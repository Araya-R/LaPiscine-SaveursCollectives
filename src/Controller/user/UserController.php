<?php

namespace App\Controller\user;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class UserController extends AbstractController
{

    //SHOW USER PROFILE
    #[Route('/user/profile', name: 'user-show-profile')]
    public function showProfile() {
        $user=$this->getUser();

        if(!$user){
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir votre profil.');
        }
        return $this->render('user/profile/showProfile.html.twig', ['user'=>$user]);
    }

//-----------------------------------------------------------------------------------------------//

    //UPDATE USER PROFILE
    #[Route('/user/update-profile', name:'update-profile')]
    public function updateProfile(Request $request, EntityManagerInterface $entityManager){
        
        $user = $this->getUser();

        if(!$user){
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir votre profil.');
        }

        if($request->isMethod('POST')){
            $pseudo=$request->request->get('pseudo');
            $email=$request->request->get('email');

            if(empty($pseudo) || empty($email)){
                $this->addFlash('error', 'Tous les champs sont requis.');

            }else{
                $user->setPseudo($pseudo);
                $user->setEmail($email);

            }

        }
    }
}

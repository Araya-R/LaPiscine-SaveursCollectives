<?php

namespace App\Controller\user;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use PHPUnit\Framework\MockObject\Rule\Parameters;

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
    public function updateProfile(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher,ParameterBagInterface $parameterBag){
        
        //dans le controller $this->getUser() retourne parfois UserInterface et non User
        //il faut type-hinter $user explicitement

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        if(!$user){
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir votre profil.');
        }

        if($request->isMethod('POST')){
            $pseudo=$request->request->get('pseudo');
            $email=$request->request->get('email');
            $password=$request->request->get('password');
            $profileImage=$request->files->get('profileImage');
            $profileImageName=null;
                if($profileImage){
                    $profileImageName=uniqid() . '.' . $profileImage->guessExtension();
                    $profileImage->move($parameterBag->get(name: 'kernel.project_dir') . '/public/uploads/users', $profileImageName);
                    $user->setImage($profileImageName);
                }

            if(empty($pseudo) || empty($email)){
                $this->addFlash('error', 'Tous les champs sont requis.');

            }else{
                $user->setPseudo($pseudo);
                $user->setEmail($email);
                $user->setUpdatedAt(new \DateTimeImmutable());
                

                if(!empty($password)){
                    //hasher de nouveau mot de passe
                    $hashedPassword=$passwordHasher->hashPassword($user, $password);
                    $user->setPassword($hashedPassword);
                }

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Votre profil mis à jour avec succès.');
                return $this->redirectToRoute('user-show-profile');
            }
        }
        return $this->render('user/profile/updateProfile.html.twig', ['user'=>$user]);
    }
}
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

    // affichage du profil utilisateur/accessible uniquement si l'user est connecté
    public function showProfile() {
        
        // on récupère l'user connecté
        $user=$this->getUser();

        // bloque l'accès si l'user n'est pas connecté
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
        
        // sécurité: accès uniquement pour l'user connecté
        if(!$user){
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir votre profil.');
        }

        // on vérifie que le formulaire a été soumis
        if($request->isMethod('POST')){
            
            // on récupère les contents des champs (champs textes/fichiers uploadés)
            $pseudo=$request->request->get('pseudo');
            $email=$request->request->get('email');
            $password=$request->request->get('password');
            $profileImage=$request->files->get('profileImage');
            $profileImageName=null;

                // Gestion de l'image de profil
                if($profileImage){

                    // pour éviter les conflits de noms
                    $profileImageName=uniqid() . '.' . $profileImage->guessExtension();
                    
                    // on enregitre l'image
                    $profileImage->move($parameterBag->get(name: 'kernel.project_dir') . '/public/uploads/users', $profileImageName);
                    // on stocke le nom en base
                    $user->setImage($profileImageName);
                }

                // Si les champs obligatoire sont vide affiche message erreur
                if(empty($pseudo) || empty($email)){
                $this->addFlash('error', 'Tous les champs sont requis.');
                
                // Sinon mise à jour des données
                }else{
                $user->setPseudo($pseudo);
                $user->setEmail($email);
                $user->setUpdatedAt(new \DateTimeImmutable());
                
                // optionnelle : mise à jour du mot de passe
                if(!empty(trim($password))){ //fonction trim => supprime les espaces au début et à la fin de la chaine de caractère $password
                    //hasher de nouveau mot de passe
                    $hashedPassword=$passwordHasher->hashPassword($user, $password);
                    $user->setPassword($hashedPassword);
                }

                $entityManager->flush(); // on sauvegarde ici pas besoin de persist car l'user existe déjà

                $this->addFlash('success', 'Votre profil mis à jour avec succès.');
                return $this->redirectToRoute('user-show-profile');
            }
        }
        return $this->render('user/profile/updateProfile.html.twig', ['user'=>$user]);
    }
}
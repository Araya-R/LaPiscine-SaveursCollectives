<?php

namespace App\Controller\admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminUserController extends AbstractController{

    // AFFICHER TOUS LES USERS 
    #[Route('/admin/display-users', name:'admin-display-users')]
    #[IsGranted('ROLE_ADMIN')]
    public function displayUsers(UserRepository $userRepository){
        $users = $userRepository->findAll();
        return $this->render('admin/displayUsers.html.twig', [
            'users' => $users
        ]);
    }

// -----------------------------------------------------------------------------------------------------------------//

    // AFFICHER LE PROFIL D'UN USER
    #[Route('/admin/user/{id}', name:'admin-show-Userprofile', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showUserProfile(int $id, UserRepository $userRepository) {
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
        return $this->render('admin/showUserProfile.html.twig', [
            'user' => $user
        ]);
    }

// -----------------------------------------------------------------------------------------------------------------//

    // SUPPRESSION USER
    #[Route('/admin/delete-user/{id}', name:'admin-delete-user', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request){

        $user= $userRepository->find($id);
        if (!$user){
            $this->addFlash('error', 'Utilisateur introuvable');
            return $this->redirectToRoute('admin-display-users');
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer un administrateur');
            return $this->redirectToRoute('admin-display-users');
        }

        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('success', 'Utilisateur supprimé avec succès');
        return $this->redirectToRoute('admin-display-users');
    }
}
<?php 

namespace App\Controller\guest;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController{
    #[Route('/login', name:'login'), ]
    public function Login (AuthenticationUtils $authenticationUtils, Security $security): Response{

        //Si déjà connecté, on redirige selon le rôle
        if ($security->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('admin-home');
        }
        if ($security->isGranted('ROLE_USER')){
            return $this->redirectToRoute('user-home');
        }

        //sinon on affiche le formulaire 
        $error = $authenticationUtils->getLastAuthenticationError();
        //renvoie une erreur si la tentative précédente a échoué
        $lastUsername = $authenticationUtils->getLastUsername();
        //récupère le dernier email ou login, pour préremplir le champ de saisie

        return $this->render('guest/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
        ]);
    }

    #[Route('/logout', name:'logout')]
    public function logout(): never
    {
        throw new \LogicException('Cette méthode peut rester vide - elle est interceptée par le firewall de logout de Symfony.');
    }

}
    
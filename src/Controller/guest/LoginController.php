<?php 

namespace App\Controller\guest;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController{
    #[Route('/login', name:'login'), ]
    public function Login (AuthenticationUtils $authenticationUtils): Response{

        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('guest/login.html.twig', ['error' => $error]);
    }

    #[Route('/logout', name:'logout')]
    public function logout(): never
    {
        throw new \LogicException('Cette méthode peut rester vide - elle est interceptée par le firewall de logout de Symfony.');
    }

}
    
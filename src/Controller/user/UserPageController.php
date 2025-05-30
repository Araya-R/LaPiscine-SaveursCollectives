<?php 

namespace App\Controller\user;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserPageController extends AbstractController{
    #[Route('/user', name:'user-home')]
    public function index(): Response{
        return $this->render('user/home.html.twig');
    }
    #[Route('/user/about', name:'user-about')]
    public function about(): Response{
        return $this->render('user/about.html.twig');
    }

    #[Route('/user/404', name:'user-404')]
    public function DisplayPage404(){
        return $this->render('user/404.html.twig');
    }
}
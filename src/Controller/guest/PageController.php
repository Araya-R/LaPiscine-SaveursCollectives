<?php 

namespace App\Controller\guest;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController{

    #[Route('/', name:'guest-home')]
    public function index(): Response{
        return $this->render('guest/home.html.twig');
    }

    #[Route('/guest/about', name:'guest-about')]
    public function about(): Response{
        return $this->render('guest/about.html.twig');
    }

    #[Route('/guest/contact', name:'guest-contact')]
    public function contact(): Response{
        return $this->render('guest/contact.html.twig');
    }

    #[Route('/404', name:'404')]
    public function DisplayPage404(): Response{
        return $this->render('guest/404.html.twig');
    }
}
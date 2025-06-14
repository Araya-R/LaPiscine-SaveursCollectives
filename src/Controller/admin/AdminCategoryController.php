<?php

namespace App\Controller\admin;

use Doctrine\ORM\EntityManagerInterface;
use Proxies\__CG__\App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use function PHPUnit\Framework\returnSelf;

class AdminCategoryController extends AbstractController{

    #[Route('/admin/category/new', name:'admin-category-new')]
    #[IsGranted('ROLE_ADMIN')]
    public function newCategory(Request $request, EntityManagerInterface $entityManager){
        
        if($request -> isMethod('POST')){
            $newCategory = $request->request->get('category');

            if(empty(trim($newCategory))){
                $this->addFlash('error', 'Le nom de la catégorie est requise');
                return $this->redirectToRoute('admin-category-new');
            }

            $category= new Category();
            $category->setTitle($newCategory);
            $category->setCreatedAt(new \DateTimeImmutable());
            $category->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Catégorie ajoutée avec succès.');
            return $this->redirectToRoute('user-categories');
        }
        return $this->render('admin/categories/createCategory.html.twig');
    }
}
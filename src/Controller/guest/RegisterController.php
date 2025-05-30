<?php

namespace App\Controller\guest;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use PhpParser\Node\Name;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;



class RegisterController extends AbstractController
{

    #[Route('/register', name: 'register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        ParameterBagInterface $parameterBag
    ) {
        if ($request->isMethod('POST')) {
            $pseudo = $request->request->get('pseudo');
            $email = $request->request->get('email');
            $password = $request->request->get('password');

            if (empty($pseudo) || empty($email) || empty($password)) {
                $this->addFlash('error', 'Tous les champs sont obligatoires !');
                return $this->redirectToRoute('register');
            }

            try {
                $user = new User();
                $user->setPseudo($pseudo);
                $user->setEmail($email);

                //traitement de l'image (si présente)
                $profileImage = $request->files->get('profileImage');

                if ($profileImage) { //si image présente
                    $profileImageName = uniqid() . '.' . $profileImage->guessExtension(); //gère un nom unique
                    $profileImage->move($parameterBag->get(name: 'kernel.project_dir') . '/public/uploads/users', $profileImageName); //déplace l'image 
                    $user->setImage($profileImageName); //stock le nom dans l'entité user
                }

                //HASHAGE DU MOT DE PASSE
                $passwordHashed = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($passwordHashed);
                $user->setRoles(['ROLE_USER']);
                $user->setCreatedAt(new \DateTimeImmutable());
                $user->setUpdatedAt(new \DateTimeImmutable());

                $entityManager->persist($user);

                $entityManager->flush();

                $this->addFlash('success', 'Votre compte a bien été créé ! Vous pouvez maintenant vous connecter!');
                return $this->redirectToRoute('login');
            } catch (\Exception $e) {
                if ($e->getCode() == 1062) {
                    $this->addFlash('error', 'Cet email existe déjà!');
                    return $this->redirectToRoute('register');
                }
            }
        }
        return $this->render(
            'guest/register.html.twig'
        );
    }
}

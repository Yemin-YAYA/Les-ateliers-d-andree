<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UsersAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UsersAuthenticator $authenticator, UserPasswordHasherInterface $userPasswordHasher, 
   EntityManagerInterface $entityManager, userAuthenticatorInterface $userAuthenticator): Response
    {
        //Création d'un nouvel utilisateur et gestion du formulaire
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        //Vérifiacation et gestion du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $avatarFile = $form->get('avatar')->getData();

            //gestion de l'avatar
            if ($avatarFile) {
                // Placez l'avatar dans le bon répertoire (avatars) evite les conflits de nomage
                $newFileName = uniqid().'.'.$avatarFile->getExtension();
                // Déplacer l'avatar
                $avatarFile->move(
                    $this->getParameter('kernel.project_dir').'/public/divers/avatars',
                    $newFileName
                );
                //Mettre à jours l'avatar dans l'entité USER
                $user->setAvatar($newFileName);
            }

            // Hachage du mot de passe
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            //Sauvegarde de l'utilisateur
            $entityManager->persist($user);
            $entityManager->flush();

            // Authetification apres inscription
            return $userAuthenticator->authenticateUser(
                $user, 
                $authenticator,
                $request
            );
        }    
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}

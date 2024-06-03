<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Security\AppAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class LoginController extends AbstractController
{
 private $em;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/registration', name: 'userRegistration', methods: ['GET', 'POST'])]
    public function userRegistration(Request $request, AuthenticationUtils $authenticationUtils): Response 
    {
        // Necesitas pasar el passwordHasher al constructor de User
        $user = new User($this->passwordHasher);
        $registrationForm = $this->createForm(UserType::class, $user);
        $registrationForm->handleRequest($request);


        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            $user = $registrationForm->getData();
            $plainPassword = $registrationForm->get('plainPassword')->getData();

            // Hashing de la contraseña
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $plainPassword
            );
            
            // Establecer la contraseña hasheada en el usuario
            $user->setPassword($hashedPassword);

            $user->setRoles(['ROLE_USER']);
            $this->em->persist($user);
            $this->em->flush();
            return $this->redirectToRoute('userRegistration');

            return $this->redirectToRoute('inicio');
        }
        return $this->render('user/login.html.twig', [
            'registration_form' => $registrationForm->createView(),
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
}



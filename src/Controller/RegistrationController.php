<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

#[Route('/api', name: 'api_')]
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function index(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $em = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);
        $username = $data['username'];
        $pass = $data['password'];

        $user = new User();
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $pass
        );
        $user->setPassword($hashedPassword);
        $user->setUsername($username);
        $em->persist($user);
        $em->flush();

        return $this->json(['message' => 'Success']);
    }
}

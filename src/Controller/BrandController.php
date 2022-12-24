<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Brand;

#[Route('/api', name: "api_")]
class BrandController extends AbstractController
{
    #[Route('/brand', name: 'app_brand')]
    public function index(): Response
    {
        return $this->render('brand/index.html.twig', [
            'controller_name' => 'BrandController',
        ]);
    }
}

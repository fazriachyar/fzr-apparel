<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Category;

#[Route('/api', name: "api_")]
class CategoryController extends AbstractController
{
    #[Route('/category/view', name: 'view_category', methods: ['GET', 'HEAD'])]
    public function viewAction(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $viewAllCategory = $em->getRepository(Category::class)
            ->findBy([
                "action" => ['I','U']
            ]);
        
        return $this->json($viewAllCategory);
    }

    #[Route('/category/view/{id}', name: 'view_category', methods: ['GET', 'HEAD'])]
    public function viewByIdAction(ManagerRegistry $doctrine, int $id): Response
    {
        $em = $doctrine->getManager();
        $viewByIdCategory = $em->getRepository(Category::class)
            ->findOneBy([
                "id" => $id,
                'action' => ['U','I']
            ]);

        if(!$viewByIdCategory){
            $viewByIdCategory = ["messages" => "Category not found"];
        }

        return $this->json($viewByIdCategory);
    }

    #[Route('/category/delete', name: 'delete_category', methods: ['POST'])]
    public function deleteAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);

        $category = $em->getRepository(Category::class)
            ->findOneBy([
                'id' => $data['id']
            ]);
        if(!$category){
            $category = ["messages" => "Category not found"];
        }
        $category->setAction('D');
        
        $em->persist($category);
        $em->flush();

        return $this->json(['message' => 'success delete data']);
    }
}

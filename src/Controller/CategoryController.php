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
    #[Route('/category/add', name: 'add_category', methods: ['POST'])]
    public function addProduct(ManagerRegistry $doctrine,Request $request): Response
    {   
        $data = json_decode($request->getContent(), true);
        $em = $doctrine->getManager();

        $checkCategory = $em->getRepository(Category::class)
            ->findOneBy([
                "name" => $data['name'],
                "action" => ['U','I']
            ]);

        if($checkCategory){
            $message['response']['failed'] = $data['name'].' Category is available !';
        } else {
            $category = new Category();
            $category->setName($data['name']);
            $category->setBrandId($data['brandId']);
            $category->setAction('I');
            $category->setAddTime(new \Datetime());
    
            $em->persist($category);
            $em->flush();
            
            $message['response']['success'] = 'Category berhasil ditambahkan..';
        }
        return $this->json($message);
    }

    #[Route('/category/view', name: 'viewall_category', methods: ['GET', 'HEAD'])]
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
            $viewByIdCategory['response']['failed'] = 'Data id '.$id.' not found !';
        }

        return $this->json($viewByIdCategory);
    }

    #[Route('/category/edit', name: 'edit_category', methods: ['PUT'])]
    public function editAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);

        $category = $em->getRepository(Category::class)
            ->findOneBy([
                'id' => $data['id'],
                'action' => ['U','I']
            ]);

        if(!$category){
            $category['response']['failed'] = "category ".$data['id']." not found";
        } else {
            $category->setName($data['name']);
            $category->setBrandId($data['brandId']);
            $category->setAction('U');
            $category->setAddTime(new \DateTime());

            $em->persist($category);
            $em->flush();
            
            $message['response']['success'] = 'Success Update '.$category->getName().' Data';
        }
        
        return $this->json($message);
    }

    #[Route('/category/delete', name: 'delete_category', methods: ['POST'])]
    public function deleteAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);

        $category = $em->getRepository(Category::class)
            ->findOneBy([
                'id' => $data['id'],
                'action' => ['U','I']
            ]);

        if(!$category){
            $message['response']['failed'] = 'Data id '.$data['id'].' not found !';
        } else {
            $category->setAction('D');
            $em->persist($category);
            $em->flush();

            $message['response']['success'] = 'Success Delete '.$category->getName().' Data';
        }
            
        return $this->json($message);
    }
}

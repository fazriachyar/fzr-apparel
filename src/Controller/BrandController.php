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
    #[Route('/brand/add', name: 'add_brand', methods: ['POST'])]
    public function addProduct(ManagerRegistry $doctrine,Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $em = $doctrine->getManager();

        $checkBrand = $em->getRepository(Brand::class)
            ->findOneBy([
                "name" => $data['name'],
                "action" => ['U','I']
            ]);

        if($checkBrand){
            $message['response']['failed'] = $data['name'].' Brand is available !';
        } else {
            $brand = new Brand();
            $brand->setName($data['name']);
            $brand->setAction('I');
            $brand->setAddTime(new \Datetime());
    
            $em->persist($brand);
            $em->flush();
            
            $message['response']['success'] = 'Brand berhasil ditambahkan..';
        }
        return $this->json($message);
    }

    #[Route('/brand/view', name: 'viewall_brand', methods: ['GET', 'HEAD'])]
    public function viewAction(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $viewAllBrand = $em->getRepository(Brand::class)
            ->findBy([
                "action" => ['I','U']
            ]);
        
        return $this->json($viewAllBrand);
    }

    #[Route('/brand/view/{id}', name: 'view_brand', methods: ['GET', 'HEAD'])]
    public function viewByIdAction(ManagerRegistry $doctrine, int $id): Response
    {
        $em = $doctrine->getManager();
        $viewByIdBrand = $em->getRepository(Brand::class)
            ->findOneBy([
                "id" => $id,
                'action' => ['U','I']
            ]);

        if(!$viewByIdBrand){
            $viewByIdBrand['response']['failed'] = 'Data id '.$id.' not found !';
        }

        return $this->json($viewByIdBrand);
    }

    #[Route('/brand/edit', name: 'edit_brand', methods: ['PUT'])]
    public function editAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);

        $brand = $em->getRepository(Brand::class)
            ->findOneBy([
                'id' => $data['id'],
                'action' => ['U','I']
            ]);

        if(!$brand){
            $brand['response']['failed'] = "bra$brand ".$data['id']." not found";
        } else {
            $brand->setName($data['name']);
            $brand->setAction('U');
            $brand->setAddTime(new \DateTime());

            $em->persist($brand);
            $em->flush();
            
            $message['response']['success'] = 'Success Update '.$brand->getName().' Data';
        }
        
        return $this->json($message);
    }

    #[Route('/brand/delete', name: 'delete_brand', methods: ['POST'])]
    public function deleteAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);

        $brand = $em->getRepository(Brand::class)
            ->findOneBy([
                'id' => $data['id'],
                'action' => ['U','I']
            ]);

        if(!$brand){
            $message['response']['failed'] = 'Data id '.$data['id'].' not found !';
        } else {
            $brand->setAction('D');
            $em->persist($brand);
            $em->flush();

            $message['response']['success'] = 'Success Delete '.$brand->getName().' Data';
        }
            
        return $this->json($message);
    }
}

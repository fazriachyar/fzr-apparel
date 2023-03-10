<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Product;
use Faker\Factory;
use Faker\Generator;

#[Route('/api', name: "api_")]
class ProductController extends AbstractController
{
    /** @var Generator */
    protected $faker;

    #[Route('/product/add', name: 'add_product', methods: ['POST'])]
    public function addProduct(ManagerRegistry $doctrine,Request $request): Response
    {   
        $data = json_decode($request->getContent(), true);
        $em = $doctrine->getManager();

        $product = new Product();
        $product->setName($data['name']);
        $product->setQuantity($data['quantity']);
        $product->setCategoryId($data['categoryId']);
        $product->setPrice($data['price']);
        $product->setAction('I');
        $product->setAddTime(new \Datetime());

        $em->persist($product);
        $em->flush();
        
        $message['response']['success'] = 'Product berhasil ditambahkan..';
        return $this->json($message);
    }

    #[Route('/product/view', name: 'view_product', methods: ['GET'])]
    public function viewAction(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $viewAllProduct = $em->getRepository(Product::class)
            ->findBy([
                "action" => ['I','U']
            ]);

        if(!$viewAllProduct){
            $viewAllProduct['response']['failed'] = "Product not found";
        }
        
        return $this->json($viewAllProduct);
    }

    #[Route('/product/view/{id}', name: 'view_productById', methods: ['GET', 'HEAD'])]
    public function viewByIdAction(ManagerRegistry $doctrine, int $id): Response
    {
        $em = $doctrine->getManager();
        $viewByIdProduct = $em->getRepository(Product::class)
            ->findOneBy([
                "id" => $id,
                "action" => ['U','I']
            ]);

        if(!$viewByIdProduct){
            $viewByIdProduct['response']['failed'] = "Product not found";
        }

        return $this->json($viewByIdProduct);
    }

    #[Route('/product/faker', name: 'mock_prouct', methods: ['POST'], )]
    public function fakeAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $hasAccess = $this->isGranted('ROLE_ADMIN');
        if(!$hasAccess){
            // $this->denyAccessUnlessGranted('ROLE_ADMIN');
            $message['response']['failed'] = 'Access Denied !';
            return $this->json($message);
        }

        $em = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);
        $num = $data['qty'];
        $this->faker = Factory::create();

        for ($i = 0; $i < $num; $i++) {
            $mock = new Product();
            $mock->setName($this->faker->word());
            $mock->setQuantity($this->faker->numberBetween(0,500));
            $mock->setPrice($this->faker->numberBetween(20000,100000));
            $mock->setCategoryId($this->faker->numberBetween(1,10));
            $mock->setAction("I");
            $mock->setAddTime($this->faker->dateTime());
            $em->persist($mock);
        }
        $em->flush();

        $message['response']['success'] = 'Success add '.$i.' mock data';
        return $this->json($message);
    }

    #[Route('/product/edit', name: 'edit_product', methods: ['PUT'])]
    public function editAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);

        $product = $em->getRepository(Product::class)
            ->findOneBy([
                'id' => $data['id']
            ]);

        if(!$product){
            $product['response']['failed'] = "Product not found";
        } else {
            $product->setName($data['name']);
            $product->setQuantity($data['quantity']);
            $product->setAction('U');
            $product->setAddTime(new \DateTime());
            $product->setPrice($data['price']);
            $product->setCategoryId($data['categoryId']);
            $em->persist($product);
            $em->flush();
        }
        
        $message['response']['success'] = 'Success Update '.$product->getName().' Data';
        return $this->json($message);
    }

    #[Route('/product/delete', name: 'delete_product', methods: ['POST'])]
    public function deleteAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);

        $product = $em->getRepository(Product::class)
            ->findOneBy([
                'id' => $data['id']
            ]);

        if(!$product){
            $product['response']['failed'] = "Product not found";
        }

        $product->setAction('D');
        $em->persist($product);
        $em->flush();

        $message['response']['success'] = "success delete data";
        return $this->json($message);
    }
}

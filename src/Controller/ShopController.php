<?php

namespace App\Controller;

use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;

class ShopController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ProductRepository $repo): Response
    {
        $products = $repo->findAll();
        return $this->render('shop/index.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/new-product", name="make-product")
     */
    public function makeProduct(Request $request, EntityManagerInterface $manager) : Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if(!$product->getId()){
                $product->setDatePosted(new \Date());
            }
            $manager->persist($product);
            $manager->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('shop/create.html.twig', [
            'formProduct' => $form->createView()]);
    }
}

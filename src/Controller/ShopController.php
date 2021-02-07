<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use Symfony\Component\Validator\Constraints\Date;

class ShopController extends AbstractController
{
    /**
     * @Route("/category/{id}", name="category")
     * @Route("/", name="home")
     */
    public function index(ProductRepository $repo, $id=null): Response
    {
        if($id){
            $products = $repo->createQueryBuilder("c")
                ->where('c.category = :categoryid')
                ->setParameter('categoryid', $id)
                ->getQuery()
                ->getResult();
        }
        else {
            $products = $repo->findAll();
        }
        return $this->render('shop/index.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/new-product", name="make-product")
     */
    public function makeProduct(Request $request, EntityManagerInterface $manager, CategoryRepository $catRepo) : Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if(!$product->getId()){
                $product->setDatePosted(new \DateTime());
            }
            $manager->persist($product);
            $manager->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('shop/create.html.twig', [
            'formProduct' => $form->createView(),
            'categories' => $catRepo->findAll()
            ]);
    }

//    /**
//     * @Route("/category/{id}", name="category")
//     */
//    public function showCategory($id, ProductRepository $repo) : Response
//    {
//        $products = $repo->createQueryBuilder("c")
//            ->where('c.category = :categoryid')
//            ->setParameter('categoryid', $id)
//            ->getQuery()
//            ->getResult();
//        return $this->render('shop/category.html.twig', [
//            'products' => $products
//        ]);
//    }
}

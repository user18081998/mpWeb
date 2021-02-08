<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Date;

class ShopController extends AbstractController
{
    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    /**
     * @Route("/category/{id}", name="category")
     * @Route("/", name="home")
     */
    public function index(ProductRepository $repo, $id=null, CategoryRepository $catRepo, ProductRepository $prepo): Response
    {
        if($id){
            $products = $repo->createQueryBuilder("c")
                ->where('c.category = :categoryid')
                ->setParameter('categoryid', $id)
                ->getQuery()
                ->getResult();
        }
        else {
            $products = $prepo->createQueryBuilder("p")->orderBy('p.id','DESC')->setMaxResults(10)->getQuery()->getResult();

        }
        return $this->render('shop/index.html.twig', [
            'products' => $products,
            'categories' => $catRepo->findAll()
        ]);
    }

    /**
     * @Route("/product/{id}/edit", name="edit-product")
     * @Route("/new-product", name="make-product")
     */
    public function makeProduct(Product $product=null,Request $request, EntityManagerInterface $manager, CategoryRepository $catRepo) : Response
    {
        if(!$product) {
            $product = new Product();
        }
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if(!$product->getId()){
                $product->setDatePosted(new \DateTime());
                $product->setUser($this->security->getUser());
            }
            $manager->persist($product);
            $manager->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('shop/create.html.twig', [
            'formProduct' => $form->createView(),
            'categories' => $catRepo->findAll(),
            'editMode' => $product->getId() !== null
            ]);
    }

    /**
     * @Route("/user/{id}", name="user")
     */
    public function showUser(User $user=null, CategoryRepository $catRepo){
        return $this->render('shop/user.html.twig',[
                'user' => $user,
                'categories' => $catRepo->findAll()
            ]);
    }

    /**
     * @Route("/about-us", name="about-us")
     */
    public function aboutUs(CategoryRepository $catRepo){
        return $this->render('shop/about_us.html.twig',[
            'categories' => $catRepo->findAll()
        ]);
    }

    /**
     * @Route("/contact-us", name="contact")
     * @return Response
     */
    public function contactUs(CategoryRepository $catRepo){
        return $this->render('shop/contact_us.html.twig',[
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

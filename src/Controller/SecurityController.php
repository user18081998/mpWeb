<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\UserType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/signup", name="signup")
     */
    public function signup(
        Request $request, EntityManagerInterface $manager,UserPasswordEncoderInterface $encoder,CategoryRepository $catRepo): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('security_login');
        }

        return $this->render( 'security/signup.html.twig',[
            'form' => $form->createView(),
            'categories' => $catRepo->findAll()
        ]);
    }

    /**
     * @Route("/login", name="login")
     * @return Response
     */
    public function login(CategoryRepository $catRepo){
        return $this->render('security/login.html.twig',[
            'categories' => $catRepo->findAll()
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(){

    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Member;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UserType;


class UserController extends AbstractController
{
    /**
     * @Route("/registerall", name="registerall")
     */
    public function registerall(Request $request)
    {
        $user = new User;

        $form = $this -> createForm(UserType::class, $user);
        $form -> handleRequest($request);
        if($form -> isSubmitted() && $form -> isValid()){
            $manager = $this -> getDoctrine() -> getManager();
            $manager -> persist($user); //commit(git)
            $manager -> flush(); // push(git)
            $this -> addFlash('success',"Le post " . $user -> getId() . ' a bien été ajouté');
            return $this->redirectToRoute('registerMember');
        }
        return $this->render('user/registerall.html.twig', ['UserForm' => $form -> createView()]);
    } 

    /**
     * @Route("/registerMember", name="registerMember")
     */
    public function registerMember(Request $request)
    {
        $member = new Member;

        $form = $this -> createForm(UserType::class, $member);
        $form -> handleRequest($request);
        if($form -> isSubmitted() && $form -> isValid()){
            $manager = $this -> getDoctrine() -> getManager();
            $manager -> persist($member); //commit(git)
            $manager -> flush(); // push(git)
            $this -> addFlash('success',"Le post " . $member -> getId() . ' a bien été ajouté');
            return $this->redirectToRoute('profil');
        }
        return $this->render('user/registerall.html.twig', ['memberForm' => $form -> createView()]);
    } 

    /**
     * @Route("/login", name="login")
     */
    public function login()
    {
        
    }

    /**
     * @Route("/profil", name="profil")
     */
    public function profil()
    {
        return $this->render('user/profil.html.twig', []);
    }

    /**
     * @Route("/profil/edit/{id}", name="editProfil")
     */
    public function editProfil($id)
    {
        
    }

    /**
     * @Route("/forgetPassword", name="forgetPassword")
     */
    public function forgetPassword()
    {
        
    }

    /**
     * @Route("/addGame", name="addGame")
     */
    public function addGame()
    {
        
    }

     /**
     * @Route("/addComment", name="addComment")
     */
    public function addComment()
    {
        
    }




}

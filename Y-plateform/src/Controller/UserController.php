<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register()
    {
        return $this->render('user/register.html.twig', []);
    } 

    /**
     * @Route("/login", name="login")
     */
    public function login()
    {
        
    }

    /**
     * @Route("/profil/{id}", name="profil")
     */
    public function profil($id)
    {
        
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

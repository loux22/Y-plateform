<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/games", name="games")
     * voir tous les jeux
     */
    public function games()
    {
        
    }

    /**
     * @Route("/game/{id}", name="game")
     * voir un jeux
     */
    public function game($id)
    {
    
    }

    /**
     * @Route("/removeGame/{id}", name="removeGame")
     */
    public function removeGame($id)
    {
    
    }

    /**
     * @Route("/verifyGame/{id}", name="verifyGame")
     */
    public function verifyGame($id)
    {
    
    }

    /**
     * @Route("/removeComment/{id}", name="removeComment")
     */
    public function removeComment($id)
    {
    
    }

    /**
     * @Route("/members", name="members")
     * voir tous les membres
     */
    public function members()
    {
        
    }

    /**
     * @Route("/member/{id}", name="member")
     * voir un membres
     */
    public function member()
    {
        
    }

    /**
     * @Route("/deleteMember/{id}", name="deleteMember")
     * voir un membres
     */
    public function deleteMember()
    {
        
    }

}

<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Member;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;

class AdminController extends AbstractController
{

      /**
     * @Route("/loginAdmin", name="loginAdmin")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('admin/loginAdmin.html.twig', [
            'error' => $error
        ]);
    }
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
    * @Route("/dashboardAdmin", name="dashboardAdmin")
    */

    public function dashboardAdmin() {
        $repository = $this->getDoctrine()->getRepository(Game::class);
        $nbDownload = $repository->allNbDownload();
        $nbGames = $repository->allNbGames();

        $repository2 = $this->getDoctrine()->getRepository(Member::class);
        $nbMembers = $repository2->allNbMembers();

        $repository3 = $this->getDoctrine()->getRepository(User::class);
        $nbUsers = $repository3->allNbUsers();
        
        return $this->render('admin/index.html.twig', [
            'nbDownload' => $nbDownload,
            'nbGames' => $nbGames,
            'nbMembers' => $nbMembers,
            'nbUsers' => $nbUsers
        ]);
    }

    /**
    * @Route("/userList", name="userList")
    */

    public function UserList() {

        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        // for ($i=1; $i <= 100; $i++) { 
        //     $u = $users->getAge();
        //     $stringValue = $u->format('Y-m-d H:i:s');
        //     $datetime1 = new \DateTime(); // date actuelle
        //     $datetime2 = new \DateTime($stringValue);
        //     $ages = $datetime1->diff($datetime2, true)->y; // le y = nombre d'années ex : 22
        // }
        
    

        return $this->render('admin/userList.html.twig', [
            'users' => $users,
            // 'ages' => $ages
        ]);
    }

    /**
    * @Route("/memberList", name="memberList")
    */

    public function memberList() {

        $repository = $this->getDoctrine()->getRepository(Member::class);
        $members = $repository -> allMembers();


        return $this->render('admin/memberList.html.twig', [
            'members' => $members,
            // 'ages' => $ages
        ]);
    }

    /**
    * @Route("/dashboardAdmin/{id}", name="dashboardAdminMember")
    */

    public function dashboardAdminMember($id) {

        $repo = $this -> getDoctrine() -> getRepository(User::class);
        $user = $repo -> find($id);

        $repository = $this-> getDoctrine() -> getRepository(Member::class);
        $member = $repository -> getUserProfil($user);

        $repository = $this-> getDoctrine() -> getRepository(Game::class);
        $nbDownload = $repository -> findNbDownload($user);
        $nbGame = $repository -> findNbGame($user);
        

        return $this->render('admin/dashboardAdminMember.html.twig', [
            'user' => $user,
            'member' => $member,
            'nbDownload' => $nbDownload,
            'nbGame' => $nbGame
        ]);
    }
    
}

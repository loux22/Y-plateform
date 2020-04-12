<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Member;
use App\Entity\User;
use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

class AdminController extends AbstractController
{

      /**
     * @Route("/loginAdmin", name="loginAdmin")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $navbar = true;
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('admin/loginAdmin.html.twig', [
            'error' => $error,
            'navbar' => $navbar
        ]);
    }


    /**
    * @Route("/dashboard/admin", name="dashboardAdmin")
    */

    public function dashboardAdmin() {
        $navbar = false;
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
            'nbUsers' => $nbUsers,
            'navbar' => $navbar
        ]);
    }

    /**
    * @Route("/dashboard/admin/userList", name="userList")
    */

    public function UserList(Request $request, PaginatorInterface $paginator) {
        $navbar = false;

        $repository = $this->getDoctrine()->getRepository(Member::class);
        $member = $repository->findBy([
            'level' => 0
        ]);
        $donnees = [];
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();
        foreach ($member as $key => $value) {
            foreach ($users as $keys => $user) {
                if ($value -> getUser() == $user){
                    $donnees[] = $user;
                }
            }
            
        }

        // for ($i=1; $i <= 100; $i++) { 
        //     $u = $users->getAge();
        //     $stringValue = $u->format('Y-m-d H:i:s');
        //     $datetime1 = new \DateTime(); // date actuelle
        //     $datetime2 = new \DateTime($stringValue);
        //     $ages = $datetime1->diff($datetime2, true)->y; // le y = nombre d'années ex : 22
        // }

        $users = $paginator->paginate(
            $donnees, 
            $request->query->getInt('page', 1),
            20 // Nombre de résultats par page
        );
    

        return $this->render('admin/userList.html.twig', [
            'users' => $users,
            'navbar' => $navbar,
            // 'ages' => $ages
        ]);
    }

    /**
    * @Route("/dashboard/admin/memberList", name="memberList")
    */

    public function memberList(Request $request, PaginatorInterface $paginator) {
        $navbar = false;

        $repository = $this->getDoctrine()->getRepository(Member::class);
        $donnees = $repository -> allMembers();

        $members = $paginator->paginate(
            $donnees, 
            $request->query->getInt('page', 1),
            20 // Nombre de résultats par page
        );

        return $this->render('admin/memberList.html.twig', [
            'members' => $members,
            'navbar' => $navbar
            // 'ages' => $ages
        ]);
    }

    /**
    * @Route("/dashboard/admin/member/{id}", name="dashboardAdminMember")
    */

    public function dashboardAdminMember($id) {
        $navbar = false;

        $repository = $this-> getDoctrine() -> getRepository(Member::class);
        $mbr = $repository -> find($id);
        $member = $repository -> getUserProfil($mbr);

        $repository = $this-> getDoctrine() -> getRepository(Game::class);
        $nbDownload = $repository -> findNbDownload($member);
        $nbGame = $repository -> findNbGame($member);
        
        $repository = $this-> getDoctrine() -> getRepository(Comment::class);
        $nbComment = $repository -> NbAllCommentGame($member);
        

        return $this->render('admin/dashboardAdminMember.html.twig', [
            'member' => $member,
            'nbDownload' => $nbDownload,
            'nbGame' => $nbGame,
            'nbComment' => $nbComment,
            'navbar' => $navbar
        ]);
    }


    /**
    * @Route("/dashboard/admin/user/{id}", name="dashboardAdminUser")
    */

    public function dashboardAdminUser($id) {
        $navbar = false;

        $repository = $this-> getDoctrine() -> getRepository(User::class);
        $user = $repository -> find($id);

        

        return $this->render('admin/dashboardAdminUser.html.twig', [
            'user' => $user,
            'navbar' => $navbar
        ]);
    }
    
    /**
    * @Route("/dashboard/admin/user/state/{id}", name="userState")
    */

    public function userState($id) {

        $repository = $this-> getDoctrine() -> getRepository(User::class);
        $user = $repository -> find($id);

        $u = $user->getIsActiveU();

        if($u == 1) {
            $user->setIsActiveU(false);
            $manager = $this -> getDoctrine() -> getManager();
            $manager -> persist($user);
            $manager->flush();
        }  

        if($u == 0) {
            $user->setIsActiveU(true);
            $manager = $this -> getDoctrine() -> getManager();
            $manager -> persist($user);
            $manager->flush();
        }  

        return $this -> redirectToRoute('userList');
    }

    /**
    * @Route("/dashboard/admin/user/role/{id}", name="userRole")
    */

    public function userRole($id) {

        $repository = $this-> getDoctrine() -> getRepository(User::class);
        $user = $repository -> find($id);

        $repository = $this-> getDoctrine() -> getRepository(Member::class);
        $member = $repository -> find($id);

        $l = $member->getLevel();

        $r = $user->getRoles();


        if($r == 'ROLE_USER' || $l == 0) {
            $user->setRoleMember('ROLE_MEMBER');
            $member->setLevel(1);
            $manager = $this -> getDoctrine() -> getManager();
            $manager -> persist($user, $member);
            $manager->flush();
        }  

        if($r == 'ROLE_MEMBER' || $l == 1) {
            $user->setRoleUser('ROLE_USER');
            $member->setLevel(0);
            $manager = $this -> getDoctrine() -> getManager();
            $manager -> persist($user, $member);
            $manager->flush();
        }  


        return $this -> redirectToRoute('userList');
    }

    /**
     * @Route("/dashboard/admin/ajoutJeux", name="ajoutJeux")
     */

    public function ajoutJeux(Request $request, PaginatorInterface $paginator) {
        $navbar = false;
        $repoMember = $this->getDoctrine()->getRepository(Member::class);
        $members = $repoMember -> findAll();
        $repoUser = $this->getDoctrine()->getRepository(User::class);
        $users = $repoUser -> findAll();
        $repository = $this->getDoctrine()->getRepository(Game::class);
        $games = $repository -> allGames();

        $mail = [];
        foreach ($games as $key => $game) {
            foreach ($members as $keys => $member) {
                if($game -> getMember() == $member){
                    foreach ($users as $k => $user) {
                        if($member -> getUser() == $user){
                            $mail[] = $user -> getMail();
                        }
                    }
                }
            }
        }

        $games = $paginator->paginate(
            $games, 
            $request->query->getInt('page', 1),
            20 // Nombre de résultats par page
        );

        return $this->render('admin/ajoutJeux.html.twig', [
            'games' => $games,
            'navbar' => $navbar,
            'mail' => $mail
            // 'ages' => $ages
        ]);
    }

    /**
    * @Route("/dashboard/admin/game/state/{id}", name="gameState")
    */

    public function gameState(Request $request, $id) {
        $navbar = false;
        $repository = $this-> getDoctrine() -> getRepository(Game::class);
        $game = $repository -> find($id);

        $g = $game->getIsActive();

        if( isset($_POST['submit']) ) {
            if( empty($_POST['checkOn']) ) {
                $game->setIsActive(false);
                $manager = $this -> getDoctrine() -> getManager();
                $manager -> persist($game);
                $manager->flush();
            } if(!empty($_POST['checkOff'])) {
                $game->setIsActive(true);
                $manager = $this -> getDoctrine() -> getManager();
                $manager -> persist($game);
                $manager->flush();
            }  
        }
        


        // if($g == 1) {
        //     $game->setIsActive(false);
        //     $manager = $this -> getDoctrine() -> getManager();
        //     $manager -> persist($game);
        //     $manager->flush();
        // }  

        // if($g == 0) {
        //     $game->setIsActive(true);
        //     $manager = $this -> getDoctrine() -> getManager();
        //     $manager -> persist($game);
        //     $manager->flush();
        // }  

        return $this->redirectToRoute('memberDashboardGame', ['id' => $id]);
    }

}

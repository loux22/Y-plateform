<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Member;
use App\Entity\Game;
use App\Entity\Note;
use App\Form\UserType;
use App\Form\UserModifyType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function registerUser(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User;
        $member = new Member;
        // redirige si connecter
        $userLog = $this->getUser();
        if($userLog != null){
            return $this->redirectToRoute('profil');
        }

        $form = $this -> createForm(UserType::class, $user);
        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()){

            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $user->setDateU(new \DateTime());
            $user->setIsActiveU(true);
            $user->setAvatar('0.png');
            $manager = $this -> getDoctrine() -> getManager();
            $manager -> persist($user); //commit(git)
            $manager -> flush(); // push(git)

            $member->setLevel(false);
            $member->setUser($user);
            $manager -> persist($member); //commit(git)
            $manager -> flush(); // push(git)

            $this -> addFlash('success','Vous étes inscris');
            return $this->redirectToRoute('registerUser');
        }
        return $this->render('user/registerUser.html.twig', ['UserForm' => $form -> createView()]);
    } 

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('user/login.html.twig', [
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        
    }

    /**
     * @Route("/profil", name="profil")
     */
    public function profil(Request $request)
    {
        // affichage des donnes du user connecter

        // redirige si pas connecter 
        $user = $this->getUser();
        if($user === null){
            return $this->redirectToRoute('login');
        }
        $u = $user->getAge();
        $stringValue = $u->format('Y-m-d H:i:s');
        $datetime1 = new \DateTime(); // date actuelle
        $datetime2 = new \DateTime($stringValue);
        $age = $datetime1->diff($datetime2, true)->y; // le y = nombre d'années ex : 22
        // recuperer dernier avatar de l'user conneter, a deplacer dans modifier
        $lastAvatar = $user -> getAvatar();
        ////////////////////////////////////////
        $repository = $this-> getDoctrine() -> getRepository(Member::class);
        $member = $repository -> getUserProfil($user);

        //jeux du joueur
        $repository = $this-> getDoctrine() -> getRepository(Game::class);
        $game = $repository -> getGameList($user);

        //note des jeux
        $repository = $this-> getDoctrine() -> getRepository(Note::class);
        $note = $repository -> noteJ($user);

        // ajouter/modifier un avatar 
        $form = $this -> createForm(UserModifyType::class, $user);
        $form -> handleRequest($request);
        

        if($form -> isSubmitted() && $form -> isValid()){

            if($user -> getAvatar() -> getClientOriginalName() != '0.png'){
                $user -> removeFile();
                unlink($this->getParameter('upload_avatar') . $lastAvatar);
            }
            
            $file = $user->getAvatar();
            $filename = 'fichier_' . time() . '_' . rand(1,99999) . '_' . md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('upload_avatar'), $filename);
            $user-> setAvatar($filename);
           

            $manager = $this-> getDoctrine() -> getManager();
            $manager -> persist($user); //commit(git)
            $manager -> flush(); // push(git)
            $this -> addFlash('success','modification');
        }

        return $this->render('user/profil.html.twig', [
            'form' => $form -> createView(),
            'user' => $user,
            'member' => $member,
            'age' => $age,
            'game' => $game,
            'note' => $note
            ]);
    }

    /**
     * @Route("/profil/edit/{id}", name="editProfil")
     */
    public function editProfil($id)
    {
        
    }

    /**
     * @Route("/profil/{id}", name="profil_user")
     */
    public function profilUser(Request $request, $id) {

        $repo = $this -> getDoctrine() -> getRepository(User::class);
        $user = $repo -> find($id);

        $u = $user->getAge();
        $stringValue = $u->format('Y-m-d H:i:s');
        $datetime1 = new \DateTime(); // date actuelle
        $datetime2 = new \DateTime($stringValue);
        $age = $datetime1->diff($datetime2, true)->y; // le y = nombre d'années ex : 22

        $repository = $this-> getDoctrine() -> getRepository(Member::class);
        $member = $repository -> getUserProfil($user);

        $repository = $this-> getDoctrine() -> getRepository(Game::class);
        $game = $repository -> getGameList($user);

        $repository = $this-> getDoctrine() -> getRepository(Note::class);
        $note = $repository -> noteJ($id);

        return $this->render('user/profil_user.html.twig', [
            'user' => $user,
            'member' => $member,
            'age' => $age,
            'game' => $game,
            'note' => $note
        ]);
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

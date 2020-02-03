<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Member;
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
     * @Route("/register", name="registerUser")
     */
    public function registerall(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User;
        $member = new Member;

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
        return $this->render('user/registerall.html.twig', ['UserForm' => $form -> createView()]);
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
        $user = $this->getUser();
        // recuperer dernier avatar de l'user conneter, a deplacer dans modifier
        $lastAvatar = $user -> getAvatar();
        ////////////////////////////////////////:
        if($user === null){
            return $this->redirectToRoute('login');
        }
        $repository = $this-> getDoctrine() -> getRepository(Member::class);
        $member = $repository -> getUserProfil($user);


        // ajouter/modifier un avatar 
        $form = $this -> createForm(UserModifyType::class, $user);
        $form -> handleRequest($request);
        

        if($form -> isSubmitted() && $form -> isValid()){

            if($user -> getAvatar() != '0.png'){
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
            'member' => $member,
            ]);
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

<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Member;
use App\Form\UserType;
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
    public function profil()
    {
        $user = $this->getUser();
        $manager = $this-> getDoctrine() -> getManager();
        $user = $manager -> find(Member::class, $user);
        return $this->render('user/profil.html.twig', ['user' => $user]);
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

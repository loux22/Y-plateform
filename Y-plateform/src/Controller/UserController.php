<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Member;
use App\Entity\Game;
use App\Entity\Note;
use App\Form\UserType;
use App\Form\UserModifyType;
use App\Form\MemberModifyType;
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
        // redirige si connecté
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

            $this -> addFlash('success','Vous êtes inscris');
            return $this->redirectToRoute('login');
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
    public function profil(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // affichage des donnes du user connecté

        // redirige si pas connecté
        $user = $this->getUser();
        $avatarUser = $user -> getAvatar();
        if($user === null){
            return $this->redirectToRoute('login');
        }
        $u = $user->getAge();
        $stringValue = $u->format('Y-m-d H:i:s');
        $datetime1 = new \DateTime(); // date actuelle
        $datetime2 = new \DateTime($stringValue);
        $age = $datetime1->diff($datetime2, true)->y; // le y = nombre d'années ex : 22
        // recuperer dernier avatar de l'user connecté, a deplacer dans modifier
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

        // modifier le profil
        $form = $this -> createForm(UserModifyType::class, $user);
        $form -> handleRequest($request);

        $memberForm = $member[0];

        $formM = $this -> createForm(MemberModifyType::class, $memberForm);
        $formM -> handleRequest($request);
        
        $userNewPassword = $request->request->all();


        if($form -> isSubmitted() && $form -> isValid()){
            if($user -> getAvatar()){
                if($user -> getAvatar() -> getClientOriginalName() != '0.png'){
                    $user -> removeFile();
                    unlink($this->getParameter('upload_avatar') . $lastAvatar);
                }
                
                $file = $user->getAvatar();
                $filename = 'fichier_' . time() . '_' . rand(1,99999) . '_' . md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('upload_avatar'), $filename);
                $user-> setAvatar($filename);
            }else{
                $user -> setAvatar($avatarUser);
            }
            
            $manager = $this-> getDoctrine() -> getManager();
            $manager -> persist($user); //commit(git)
            $manager -> flush(); // push(git)
            $this -> addFlash('success','modification');
        }

        if($formM -> isSubmitted() && $formM -> isValid()){
            $manager = $this-> getDoctrine() -> getManager();
            $manager -> persist($memberForm); //commit(git)
            $manager -> flush(); // push(git)
            $this -> addFlash('success','modification');
            
        }

        //Changer mot de passe
        $userNewPassword = $request->request->all();
        $actuelPassword = $user->getPassword();
        if($userNewPassword){
            $userLastPassword = $userNewPassword['lastPassword'];
            $NewPassword = $userNewPassword['newPassword'];
            $userRptNewPassword = $userNewPassword['repeatNewPassword'];
            if (password_verify($userLastPassword, $actuelPassword)) {
                    if($NewPassword == $userRptNewPassword) {

                        if(strlen($NewPassword) >= 8) {
                            $newUser = new User; 
                            $password = $passwordEncoder->encodePassword($newUser, $NewPassword);
                            $user -> setPassword($password);
                            $manager = $this-> getDoctrine() -> getManager();
                            $manager -> persist($user); //commit(git)
                            $manager -> flush(); // push(git)
                            $this -> addFlash('success','Le mot de passe a été modifié !');
                        }else {
                            $this -> addFlash('errors','Le mot de passe doit faire minimum 8 caractères');
                        }
                        

        
                    } else {
                        $this -> addFlash('errors','Les deux mots de passe ne sont pas identiques');
                    }


            } else {
                $this -> addFlash('errors','Le mot de passse ne corresponds pas a celui actuel');
            }
            
        }
        


        

        return $this->render('user/profil.html.twig', [
            'form' => $form -> createView(),
            'formM' => $formM -> createView(),
            'user' => $user,
            'actuelPassword' => $actuelPassword,
            'member' => $member,
            'age' => $age,
            'game' => $game,
            'note' => $note
            ]);
    }


    /**
     * @Route("/profil/{id}", name="profil_user")
     */
    public function profilUser(Request $request, $id) {

        $repo = $this -> getDoctrine() -> getRepository(User::class);
        $user = $repo -> find($id);
        
        if($user == null) {
            return $this->redirectToRoute('profil');
        }

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

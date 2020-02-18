<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Member;
use App\Entity\Note;
use App\Form\AddGameType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GamesController extends AbstractController
{

    

    /**
     * @Route("/", name="home")
     * page d'accueil
     */
    public function home()
    {
        $repository = $this -> getDoctrine() -> getRepository(Game::class);
        $games = $repository -> lastGames();

        return $this->render('games/home.html.twig', [
            'games' => $games
        ]);
    }

    /**
     * @Route("/addgame", name="addgame")
     * page d'accueil
     */
    public function addGame(Request $request)
    {
        $user = $this->getUser();
        $repository = $this-> getDoctrine() -> getRepository(Member::class);
        $member = $repository -> getUserProfil($user);
        $member = $member[0];
        if($member -> getLevel() === false){
            return $this->redirectToRoute('profil');
        }
        $game = new Game;
        $form = $this -> createForm(AddGameType::class, $game);

        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()){

            // add img game
            $fileImg = $game->getImg();
            $filename = 'GameImg_' . time() . '_' . rand(1,99999) . '_' . md5(uniqid()) . '.' . $fileImg->guessExtension();
            $fileImg->move($this->getParameter('upload_gameImg'), $filename);
            $game-> setImg($filename);

            // add .exe game
            $fileUrl = $game->getUrl();
            $filename = 'Exe_' . time() . '_' . rand(1,99999) . '_' . md5(uniqid()) . '.' . $fileUrl->guessExtension();
            $fileUrl->move($this->getParameter('upload_gameUrl'), $filename);
            $game-> setUrl($filename);

            
            $game->setDateG(new \DateTime());
            $game->setIsActive(false);
            $game->setNbDownload(0);
            $game->setMember($member);

            $manager = $this -> getDoctrine() -> getManager();
            $manager -> persist($game); //commit(git)
            $manager -> flush(); // push(git)

            $this -> addFlash('success','Votre jeu a bien été ajouter');
            return $this->redirectToRoute('games');
        }
        return $this->render('games/addgame.html.twig', [
            'formGame' => $form -> createView(),
        ]);
    }


    /**
     * @Route("/games", name="games")
     * voir tout les jeux 
     */
    public function games()
    {
        $repository = $this-> getDoctrine() -> getRepository(Game::class);
        $game = $repository -> findAll();

        return $this->render('games/games.html.twig', ['game' => $game]);
    }

     /**
     * @Route("/games/{id}", name="game")
     * voir tout les jeux 
     */
    public function game($id)
    {
        $manager = $this-> getDoctrine() -> getManager();
        $game = $manager -> find(Game::class, $id);
        $repository = $this-> getDoctrine() -> getRepository(Note::class);
        $note = $repository -> note($game);

        return $this->render('games/game.html.twig', [
            'game' => $game,
            'note'=> $note
            ]);
    }

    

    

    /**
     * @Route("/game/{id}", name="comment")
     * voir un jeux 
     */
    public function comment()
    {
        
    }
}

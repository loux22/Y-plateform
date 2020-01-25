<?php

namespace App\Controller;

use App\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GamesController extends AbstractController
{

    /**
     * @Route("/", name="home")
     * page d'accueil
     */
    public function home()
    {
        return $this->render('games/home.html.twig', []);
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
     * @Route("/game/{id}", name="game")
     * voir un jeux 
     */
    public function game()
    {
        
    }

    /**
     * @Route("/game/{id}", name="comment")
     * voir un jeux 
     */
    public function comment()
    {
        
    }
}

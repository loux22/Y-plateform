<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Game;
use App\Entity\Note;
use App\Entity\Member;
use App\Entity\Comment;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MemberController extends AbstractController
{
     /**
     * @Route("/memberDashboard", name="memberDashboard")
     */
    public function memberDashboard()
    {
        $userLog = $this->getUser();

        $repository = $this-> getDoctrine() -> getRepository(Member::class);
        $member = $repository -> getUserProfil($userLog);
        $member = $member[0];

        $repoGame = $this-> getDoctrine() -> getRepository(Game::class);
        $repoComment = $this-> getDoctrine() -> getRepository(Comment::class);

        $nbDowloadGame = $repoGame -> findNbDownload($member);
        $nbGame = $repoGame -> findNbGame($member);

        $nbComments = $repoComment -> FindAllCommentGame($member);
        $nbComments = count($nbComments);

        return $this->render('member/dashboard.html.twig', [
            'NbDowloadGame' => $nbDowloadGame,
            'nbGame' => $nbGame,
            'member' => $member,
            'nbComments' => $nbComments
        ]);
    }

    /**
     * @Route("/memberDashboard/games", name="memberDashboardGames")
     */
    public function memberDashboardGames()
    {
        $userLog = $this->getUser();

        $repository = $this-> getDoctrine() -> getRepository(Member::class);
        $member = $repository -> getUserProfil($userLog);
        $member = $member[0];

        $repoGame = $this-> getDoctrine() -> getRepository(Game::class);

        $games = $repoGame -> getGameList($member);


        

        return $this->render('member/dashboardGames.html.twig', [
            'games' => $games,
        ]);
    }

    /**
     * @Route("/memberDashboard/game/{id}", name="memberDashboardGame")
     */
    public function memberDashboardGame($id)
    {
        $userLog = $this->getUser();

        $repository = $this-> getDoctrine() -> getRepository(Member::class);
        $member = $repository -> getUserProfil($userLog);
        $member = $member[0];

        $repoGame = $this-> getDoctrine() -> getRepository(Game::class);

        $game = $repoGame -> findBy([
            'id' => 21
        ]);

        $repository = $this->getDoctrine()->getRepository(Note::class);
        $note = $repository->note($id);

        $repo = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $repo->FindCommentGame($id);

        $rep = $this->getDoctrine()->getRepository(Category::class);
        $category = $rep->findAll();

        $game = $game[0];

        


        

        return $this->render('member/dashboardGame.html.twig', [
            'game' => $game,
            'note' => $note,
            'comments' => $comments
        ]);
    }
}

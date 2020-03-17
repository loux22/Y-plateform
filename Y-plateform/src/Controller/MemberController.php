<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Note;
use App\Entity\Member;
use App\Entity\Comment;
use App\Entity\Category;
use App\Entity\CommentLike;
use App\Form\ModifyGameType;
use Symfony\Component\HttpFoundation\Request;
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
    public function memberDashboardGame($id, Request $request)
    {
        $userLog = $this->getUser();

        $repository = $this-> getDoctrine() -> getRepository(Member::class);
        $member = $repository -> getUserProfil($userLog);
        $member = $member[0];

        $repoGame = $this-> getDoctrine() -> getRepository(Game::class);

        $game = $repoGame -> findBy([
            'id' => $id
        ]);

        $repository = $this->getDoctrine()->getRepository(Note::class);
        $note = $repository->note($id);

        $repo = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $repo->FindCommentGame($id);

        $rep = $this->getDoctrine()->getRepository(Category::class);
        $category = $rep->categorieGame($id);

        $game = $game[0];
        $gameImg = $game -> getImg();
        $form = $this->createForm(ModifyGameType::class, $game);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($game -> getImg()){
                if($game -> getImg() -> getClientOriginalName() != '0.png'){
                    unlink($this->getParameter('upload_gameImg') . $gameImg);
                }
                
                $file = $game->getImg();
                $filename = 'fichier_' . time() . '_' . rand(1,99999) . '_' . md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('upload_gameImg'), $filename);
                $game-> setImg($filename);
            }else{
                $game -> setImg($gameImg);
            }
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($game); //commit(git)
            $manager->flush(); // push(git)
            $this -> addFlash('success','le jeu a bien été modifier');
        }
        return $this->render('member/dashboardGame.html.twig', [
            'game' => $game,
            'note' => $note,
            'comments' => $comments,
            'category' => $category,
            'formModifyGame' => $form -> createView(),
        ]);
    }

    /**
     * @Route("/memberDashboard/game/delete/{id}", name="GameDelete")
     */
    public function gameDelete($id)
    {
        $manager = $this -> getDoctrine() -> getManager();
        $game = $manager -> find(Game::class, $id);

        $repo = $this-> getDoctrine() -> getRepository(Category::class);
        $categoryGame = $repo -> categorieGame($game);
        foreach ($categoryGame as $key => $value) {
            $manager -> remove($value); 
        }

        $repo = $this-> getDoctrine() -> getRepository(Note::class);
        $noteGame = $repo -> AllNoteGame($game);
        if(!empty($noteGame)){
            foreach ($noteGame as $key => $value) {
                $manager -> remove($value); 
            }
        }
        

        $repo = $this-> getDoctrine() -> getRepository(Comment::class);
        $commentGame = $repo -> findBy([
            'game' => $id
        ]);
        if(!empty($commentGame)){
            foreach ($commentGame as $key => $value) {
                $repo = $this-> getDoctrine() -> getRepository(CommentLike::class);
                $commentLikeGame = $repo -> findBy([
                    'comment' => $value -> getId()
                ]);
                if(!empty($commentLikeGame)){
                    foreach ($commentLikeGame as $keys => $values) {
                        $manager -> remove($values);
                    }
                }
                
                $manager -> remove($value); 
            }
        }
        

        $gameName = $game -> getName();
        $manager -> remove($game);
        $manager -> flush();
        $this -> addFlash('success',"Le jeu " . $gameName . ' a bien été supprimé');
        return $this->redirectToRoute('memberDashboardGames');
    }
}

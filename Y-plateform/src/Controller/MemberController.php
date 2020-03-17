<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Note;
use App\Entity\Member;
use App\Entity\Comment;
use App\Entity\Category;
use App\Form\AddGameType;
use App\Entity\CommentLike;
use App\Form\ModifyGameType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function memberDashboardGames(Request $request)
    {
        $user = $this->getUser();

        $repository = $this-> getDoctrine() -> getRepository(Member::class);
        $member = $repository -> getUserProfil($user);
        $member = $member[0];

        $repoGame = $this-> getDoctrine() -> getRepository(Game::class);

        $games = $repoGame -> getGameList($member);

        $repository = $this->getDoctrine()->getRepository(Member::class);
        $member = $repository->getUserProfil($user);
        $member = $member[0];
        if ($member->getLevel() === false) {
            return $this->redirectToRoute('profil');
        }
        $game = new Game;
        $form = $this->createForm(AddGameType::class, $game);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // add img game
            $fileImg = $game->getImg();
            $filename = 'GameImg_' . time() . '_' . rand(1, 99999) . '_' . md5(uniqid()) . '.' . $fileImg->guessExtension();
            $fileImg->move($this->getParameter('upload_gameImg'), $filename);
            $game->setImg($filename);

            // add .exe game
            $fileUrl = $game->getUrl();
            $filename = 'Exe_' . time() . '_' . rand(1, 99999) . '_' . md5(uniqid()) . '.' . $fileUrl->guessExtension();
            $fileUrl->move($this->getParameter('upload_gameUrl'), $filename);
            $game->setUrl($filename);


            $game->setDateG(new \DateTime());
            $game->setIsActive(false);
            $game->setNbDownload(0);
            $game->setPrix(0);

            $manager = $this->getDoctrine()->getManager();
            // $category = $manager->find(Category::class, $game->getCategory());
            // $game->addCategory($game->getCategory());
            $game->setMember($member);

            $manager->persist($game); //commit(git)
            $manager->flush(); // push(git)

            $this->addFlash('success', 'Votre jeu a bien été ajouter');
            return $this->redirectToRoute('memberDashboardGames');
        }
    
        return $this->render('member/dashboardGames.html.twig', [
            'games' => $games,
            'formGame' => $form->createView(),
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
            $category = new Category;
            $category -> setTitle($value -> getTitle());
            $manager -> persist($category);
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

     /**
     * @Route("/searchCategory", name="searchCategory")
     */
    public function searchCategory(Request $request): Response
    {
        $getCategory = $request->get('category');
        $repository = $this-> getDoctrine() -> getRepository(Category::class);
        $category = $repository -> searchCategory($getCategory);
        if($category){
            foreach ($category as $key => $value) {
                echo '<div> <input type="checkbox" id="-'. $value -> getId().'" name="CategoryId[]" value="'. $value -> getId().'">' . $value -> getTitle() . '<div>';
            }
        } else{
            echo 'aucune categorie trouver';
        }  
        return new Response();
    }
}

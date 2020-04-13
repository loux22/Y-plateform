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
     * @Route("/dashboard/member", name="memberDashboard")
     */
    public function memberDashboard()
    {
        // $this->denyAccessUnlessGranted('ROLE_MEMBER');
        $navbar = false;
        $userLog = $this->getUser();
        if($userLog === null){
            return $this->redirectToRoute('login');
        }

        $repository = $this->getDoctrine()->getRepository(Member::class);
        $member = $repository->getUserProfil($userLog);
        $member = $member[0];

        $repoGame = $this->getDoctrine()->getRepository(Game::class);
        $repoComment = $this->getDoctrine()->getRepository(Comment::class);

        $nbDowloadGame = $repoGame->findNbDownload($member);
        $nbGame = $repoGame->findNbGame($member);

        $nbComments = $repoComment->FindAllCommentGame($member);
        $nbComments = count($nbComments);

        return $this->render('member/dashboard.html.twig', [
            'NbDowloadGame' => $nbDowloadGame[0]['nbDownload'],
            'nbGame' => $nbGame[0]['nbGame'],
            'member' => $member,
            'nbComments' => $nbComments[0]['nbComments'],
            'navbar' => $navbar,
            'dashboard' => 1
        ]);
    }

    /**
     * @Route("/dashboard/member/games", name="memberDashboardGames")
     */
    public function memberDashboardGames(Request $request)
    {
        // $this->denyAccessUnlessGranted('ROLE_MEMBER');
        $navbar = false;
        $user = $this->getUser();
        if($user === null){
            return $this->redirectToRoute('login');
        }

        $repository = $this->getDoctrine()->getRepository(Member::class);
        $member = $repository->getUserProfil($user);
        $member = $member[0];

        $repoGame = $this->getDoctrine()->getRepository(Game::class);

        $games = $repoGame->getGameList($member);

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
            $category = $request->request->all();

            if (isset($category['CategoryId'])) {
                $category = $category['CategoryId'];
                foreach ($category as $key => $value) {
                    $newCategory = $manager->find(Category::class, $value);
                    $game->addCategory($newCategory);
                }
                $game->setMember($member);
                $manager->persist($game);
                $manager->flush($game);
                $this->addFlash('success', 'La création de votre jeu est une réussite ');
                return $this->redirectToRoute('memberDashboardGames');
            } else {
                $this->addFlash('errors', 'tu dois ajouter au moins 1 categorie');
            }
        }

        return $this->render('member/dashboardGames.html.twig', [
            'games' => $games,
            'formGame' => $form->createView(),
            'navbar' => $navbar,
            'member' => $member,
            'dashboard' => 1
        ]);
    }

    /**
     * @Route("/dashboard/member/game/{id}", name="memberDashboardGame")
     */
    public function memberDashboardGame($id, Request $request)
    {
        $navbar = false;
        // $this->denyAccessUnlessGranted('ROLE_MEMBER');
        $pop = 0;
        if (isset($_GET['pop']) && $_GET['pop'] == 1) {
            $pop = 1;
        }
        $navbar = false;
        $userLog = $this->getUser();
        if($userLog === null){
            return $this->redirectToRoute('login');
        }

        $repository = $this->getDoctrine()->getRepository(Member::class);
        $member = $repository->getUserProfil($userLog);
        $member = $member[0];

        $repoGame = $this->getDoctrine()->getRepository(Game::class);

        $game = $repoGame->findBy([
            'id' => $id
        ]);

        $repository = $this->getDoctrine()->getRepository(Note::class);
        $note = $repository->note($id);

        $repo = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $repo->FindCommentGame($id);

        $rep = $this->getDoctrine()->getRepository(Category::class);
        $category = $rep->categorieGame($id);

        $game = $game[0];
        $gameImg = $game->getImg();
        $form = $this->createForm(ModifyGameType::class, $game);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($game->getImg()) {
                if ($game->getImg()->getClientOriginalName() != '0.png') {
                    unlink($this->getParameter('upload_gameImg') . $gameImg);
                }

                $file = $game->getImg();
                $filename = 'fichier_' . time() . '_' . rand(1, 99999) . '_' . md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('upload_gameImg'), $filename);
                $game->setImg($filename);
            } else {
                $game->setImg($gameImg);
            }
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($game); //commit(git)
            $manager->flush(); // push(git)
            $this->addFlash('success', 'le jeu a bien été modifier');
        }
        $searchCategory = $request->request->all();

        if (isset($searchCategory['CategoryId'])) {
            $manager = $this->getDoctrine()->getManager();
            $searchCategory = $searchCategory['CategoryId'];
            foreach ($searchCategory as $key => $value) {
                $newCategory = $manager->find(Category::class, $value);
                $game->addCategory($newCategory);
            }
            $game->setMember($member);
            $manager->persist($game); //commit(git)
            $manager->flush(); // push(git)
            $this->addFlash('success', 'le jeu a bien été modifier');
            return $this->redirectToRoute('memberDashboardGame', ['id' => $id, 'pop' => '1']);
        }
        return $this->render('member/dashboardGame.html.twig', [
            'game' => $game,
            'note' => $note,
            'comments' => $comments,
            'category' => $category,
            'formModifyGame' => $form->createView(),
            'navbar' => $navbar,
            'id' => $id,
            'pop' => $pop,
            'member' => $member,
            'dashboard' => 1
        ]);
    }

    /**
     * @Route("/dashboard/member/game/delete/{id}", name="GameDelete")
     */
    public function gameDelete($id)
    {
        // $this->denyAccessUnlessGranted('ROLE_MEMBER');
        $manager = $this->getDoctrine()->getManager();
        $game = $manager->find(Game::class, $id);

        $repo = $this->getDoctrine()->getRepository(Category::class);
        $categoryGame = $repo->categorieGame($game);
        foreach ($categoryGame as $key => $value) {
            $category = new Category;
            $category->setTitle($value->getTitle());
            $manager->persist($category);
            $manager->remove($value);
        }

        $repo = $this->getDoctrine()->getRepository(Note::class);
        $noteGame = $repo->AllNoteGame($game);
        if (!empty($noteGame)) {
            foreach ($noteGame as $key => $value) {
                $manager->remove($value);
            }
        }


        $repo = $this->getDoctrine()->getRepository(Comment::class);
        $commentGame = $repo->findBy([
            'game' => $id
        ]);
        if (!empty($commentGame)) {
            foreach ($commentGame as $key => $value) {
                $repo = $this->getDoctrine()->getRepository(CommentLike::class);
                $commentLikeGame = $repo->findBy([
                    'comment' => $value->getId()
                ]);
                if (!empty($commentLikeGame)) {
                    foreach ($commentLikeGame as $keys => $values) {
                        $manager->remove($values);
                    }
                }

                $manager->remove($value);
            }
        }


        $gameName = $game->getName();
        $manager->remove($game);
        $manager->flush();
        $this->addFlash('success', "Le jeu " . $gameName . ' a bien été supprimé');
        return $this->redirectToRoute('memberDashboardGames');
    }

    /**
     * @Route("/dashboard/member/game/{idGame}/category/delete/{id}", name="CategoryDelete")
     */
    public function game($idGame, $id)
    {
        // $this->denyAccessUnlessGranted('ROLE_MEMBER');
        $manager = $this->getDoctrine()->getManager();
        $game = $manager->find(Game::class, $idGame);

        $repo = $this->getDoctrine()->getRepository(Category::class);
        $categoryGame = $repo->categorieGame($game);
        foreach ($categoryGame as $key => $value) {
            if ($value->getId() == intval($id)) {
                $game->removeCategory($value);
                $this->addFlash('success', "La catégorie " . $value -> getTitle() . ' a bien été supprimé');
                $manager->persist($game);
                $manager->flush();
            }
        }
        
        return $this->redirectToRoute('memberDashboardGame', ['id' => $idGame]);
    }

    /**
     * @Route("/searchCategory", name="searchCategory")
     */
    public function searchCategory(Request $request): Response
    {


        $getCategory = $request->get('category');
        $idGame = $request->get('game');
        $repo = $this->getDoctrine()->getRepository(Game::class);
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $game = $repo->findBy([
            'id' => $idGame
        ]);
        $allCategory = $repository->categorieGame($game);
        $category = $repository->searchCategory($getCategory);
        // $verify = 0;
        if ($category) {
            foreach ($category as $key => $value) {
                // $verify = 0;
                if (!empty($allCategory)) {
                    foreach ($allCategory as $keys => $values) {
                        if ($value != $values) {
                            // $verify = 1;
                            // if ($verify != 1) {
                                echo '<label>' . $value->getTitle() . '</label> <input type="checkbox" class="search-bar" id="-' . $value->getId() . '" name="CategoryId[]" value="' . $value->getId() . '">';
                            // }
                        }
                    }
                } else {
                    echo '<label>' . $value->getTitle() . '</label> <input type="checkbox" class="search-bar" id="-' . $value->getId() . '" name="CategoryId[]" value="' . $value->getId() . '">';
                }
            }
        } else {
            echo 'aucune categorie trouver';
        }
        return new Response();
    }
}

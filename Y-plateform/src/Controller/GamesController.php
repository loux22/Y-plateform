<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Game;
use App\Entity\Note;
use App\Entity\Member;
use App\Entity\Comment;
use App\Form\AddGameType;
use App\Form\CommentType;
use App\Entity\CommentLike;
use App\Repository\CommentLikeRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
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
        $repository = $this->getDoctrine()->getRepository(Game::class);
        $games = $repository->lastGames();

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
            $game->setMember($member);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($game); //commit(git)
            $manager->flush(); // push(git)

            $this->addFlash('success', 'Votre jeu a bien été ajouter');
            return $this->redirectToRoute('games');
        }
        return $this->render('games/addgame.html.twig', [
            'formGame' => $form->createView(),
        ]);
    }


    /**
     * @Route("/library", name="library")
     * voir tout les jeux 
     */
    public function games()
    {
        $repository = $this->getDoctrine()->getRepository(Game::class);
        $games = $repository->findAll();
        $last3game = $repository->last3Games();
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $category = $repository->findAll();

        return $this->render('games/games.html.twig', [
            'games' => $games,
            'last3game' => $last3game,
            'category' => $category
        ]);
    }

    /**
     * @Route("/library/{cat}", name="category")
     * voir tout les jeux 
     */
    public function category($cat)
    {
        $repository = $this->getDoctrine()->getRepository(Game::class);
        $games = $repository->GamesCategory($cat);
        $last3game = $repository->last3Games();

        if ($cat == 'new') {
            $games = $repository->NewGames();
        } elseif ($cat == 'pop') {
            $allGames = $repository->findAll();
            $repo = $this->getDoctrine()->getRepository(Note::class);
            $games = [];
            foreach ($allGames as $key => $value) {
                $games[$key][0] = $repo->note($value);
                $games[$key][1] = $value;
            }
            foreach ($games as $key => $value) {
                foreach ($games as $keys => $values) {
                    if ($keys + 1 != count($games)) {
                        if ($games[$keys + 1][0] > $values[0]) {
                            $objet = $values;
                            $games[$keys] = $games[$keys + 1];
                            $games[$keys + 1] = $objet;
                        }
                    }
                }
            }
            foreach ($games as $key => $value) {
                if($key >= 5){
                    unset($games[$key]);
                }else{
                    $games[$key] = $value[1];
                }
            }
        } elseif ($cat == 'better') {
            $games = $repository->BetterSaleGames();
        }

        $repository = $this->getDoctrine()->getRepository(Category::class);
        $category = $repository->findAll();



        return $this->render('games/games.html.twig', [
            'games' => $games,
            'last3game' => $last3game,
            'category' => $category,
        ]);
    }

    /**
     * @Route("/games/{id}", name="game")
     * voir tout les jeux 
     */
    public function game($id, Request $request)
    {
        $userlog = $this->getUser();

        $manager = $this->getDoctrine()->getManager();
        $game = $manager->find(Game::class, $id);
        $repository = $this->getDoctrine()->getRepository(Note::class);
        $note = $repository->note($game);

        $repo = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $repo->FindCommentGame($id);

        $like = [];
        $dislike = [];
        foreach ($comments as $key => $com) {
            foreach ($com->getCommentLikes() as $keys => $value) {
                if ($value->getValue() == true) {
                    $like[$key][] = $value;
                } else {
                    $dislike[$key][] = $value;
                }
            }
            if (empty($like[$key])) {
                $like[$key][] = 0;
            }
            if (empty($dislike[$key])) {
                $dislike[$key][] = 0;
            }
        }


        $comment = new Comment;
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($userlog);
            $comment->setGame($game);
            $comment->setDateC(new \DateTime());
            $manager->persist($comment); //commit(git)
            $manager->flush(); // push(git)
            $this->addFlash('success', 'Votre commentaire a bien été ajouter');
            return $this->redirectToRoute('game', ['id' => $game->getId()]);
        }


        return $this->render('games/game.html.twig', [
            'game' => $game,
            'note' => $note,
            'comments' => $comments,
            'commentForm' => $form->createView(),
            'like' => $like,
            'dislike' => $dislike,
        ]);
    }


    /**
     * @Route("/comment/{id}/like", name="comment_like")
     * @Route("/comment/{id}/dislike", name="comment_dislike")
     * permet de liker ou unliker un article
     */
    public function like(Comment $comment, CommentLikeRepository $likeRepo, Request $request): Response
    {
        $repo = $this->getDoctrine()->getRepository(CommentLike::class);

        $url = $request->attributes->get('_route');
        $user = $this->getUser();
        $manager = $this->getDoctrine()->getManager();

        if (!$user) {
            return $this->json([
                'code' => 403,
                'message' => "pas autoriser",
            ], 403);
        }
        $a = 0;
        if ($comment->isLikedByUser($user)) {
            if ($comment->likeOrDislike($user) === true) {
                $a = 0;
                if ($url == "comment_dislike") {
                    $a = 7;
                    $like = new CommentLike();
                    $like->setComment($comment)
                        ->setUser($user)
                        ->setValue(false);
                    $manager->persist($like);
                    $manager->flush();

                    $like = $likeRepo->findOneBy([
                        'comment' => $comment,
                        'user' => $user
                    ]);

                    $manager->remove($like);
                    $manager->flush();

                    $likejson = $repo->findBy([
                        'comment' => $comment,
                        'value' => true
                    ]);
                    $dislikejson = $repo->findBy([
                        'comment' => $comment,
                        'value' => false
                    ]);

                    return $this->json([
                        'code' => 200,
                        'message' => "like supprimer",
                        'likes' => count($likejson),
                        'dislikes' => count($dislikejson),
                        'a' => $a

                    ], 200);
                }
            } elseif ($comment->likeOrDislike($user) === false) {
                $a = 0;
                if ($url == "comment_like") {
                    $a = 18;
                    $like = new CommentLike();
                    $like->setComment($comment)
                        ->setUser($user)
                        ->setValue(true);
                    $manager->persist($like);
                    $manager->flush();

                    $like = $likeRepo->findOneBy([
                        'comment' => $comment,
                        'user' => $user
                    ]);

                    $manager->remove($like);
                    $manager->flush();

                    $likejson = $repo->findBy([
                        'comment' => $comment,
                        'value' => true
                    ]);
                    $dislikejson = $repo->findBy([
                        'comment' => $comment,
                        'value' => false
                    ]);

                    return $this->json([
                        'code' => 200,
                        'message' => "like supprimer",
                        'likes' => count($likejson),
                        'dislikes' => count($dislikejson),
                        'a' => $a

                    ], 200);
                }
            }
            if ($a == 0) {
                $like = $likeRepo->findOneBy([
                    'comment' => $comment,
                    'user' => $user
                ]);

                $manager->remove($like);
                $manager->flush();

                $likejson = $repo->findBy([
                    'comment' => $comment,
                    'value' => true
                ]);
                $dislikejson = $repo->findBy([
                    'comment' => $comment,
                    'value' => false
                ]);

                return $this->json([
                    'code' => 200,
                    'message' => "like supprimer",
                    'likes' => count($likejson),
                    'dislikes' => count($dislikejson),


                ], 200);
            }
        }

        $like = new CommentLike();
        $like->setComment($comment)
            ->setUser($user);
        if ($url == "comment_like") {
            $like->setValue(true);
        } else {
            $like->setValue(false);
        }


        $manager->persist($like);
        $manager->flush();

        $likejson = $repo->findBy([
            'comment' => $comment,
            'value' => true
        ]);
        $dislikejson = $repo->findBy([
            'comment' => $comment,
            'value' => false
        ]);

        return $this->json([
            'code' => 200,
            'likes' => count($likejson),
            'dislikes' => count($dislikejson),
            'a' => $a
        ], 200);
    }
}

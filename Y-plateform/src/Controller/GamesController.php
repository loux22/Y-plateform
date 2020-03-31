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

    public function navbar(Request $request)
    {
        $currentRoute = $request->attributes->get('_route');
        $route = $this->get('router')->generate($currentRoute, [], true);
        $route = explode("/", $route);
        $navbar = true;
        if (isset($route[1])) {
            if ($route[1] === "dashboard") {
                $navbar = false;
            }
        }
        return $navbar;
    }

    /**
     * @Route("/", name="home")
     * page d'accueil
     */
    public function home()
    {
        $repository = $this->getDoctrine()->getRepository(Game::class);
        $games = $repository->lastGames();

        $navbar = true;



        return $this->render('games/home.html.twig', [
            'games' => $games,
            'navbar' => $navbar
        ]);
    }


    /**
     * @Route("/library", name="library")
     * voir tout les jeux 
     */
    public function games()
    {
        $navbar = true;

        $repository = $this->getDoctrine()->getRepository(Game::class);
        $games = $repository->findAll();
        $last3game = $repository->last3Games();
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $category = $repository->findAll();

        return $this->render('games/games.html.twig', [
            'games' => $games,
            'last3game' => $last3game,
            'category' => $category,
            'navbar' => $navbar
        ]);
    }

    /**
     * @Route("/library/{cat}", name="category")
     * voir tout les jeux 
     */
    public function category(string $cat)
    {
        $navbar = true;

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
                if ($key >= 5) {
                    unset($games[$key]);
                } else {
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
            'navbar' => $navbar
        ]);
    }

    /**
     * @Route("/games/{id}", name="game")
     * voir tout les jeux 
     */
    public function game($id, Request $request)
    {
        $navbar = true;
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
            'navbar' => $navbar
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

    /**
     * @Route("/searchGame", name="searchGame")
     */
    public function searchCategory(Request $request): Response
    {


        $getGame = $request->get('game');
        $repo = $this->getDoctrine()->getRepository(Game::class);
        $game = $repo->searchGames($getGame);
        if ($game) {
            foreach ($game as $key => $value) {
                echo '<div><a href="{{ path("game", {id : ' . $value -> getId() . '}) }}' . '" class="search-bar">' . $value -> getName() . '</a></div>';
                                    // {{ path('game', {id : game.id})}}
            }
        } else {
            echo 'aucune jeux trouvés';
        }
        return new Response();
    }
}

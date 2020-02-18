<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Game;
use App\Entity\Member;
use App\Entity\Note;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i=0; $i <= 5; $i++) { 
            $category = new Category;
            $category -> setTitle('category' . $i);
            $manager -> persist($category);
        }

        for($i = 1; $i <= 5; $i++){
            $user = new User;
            $user -> setUsername('user' . $i);
            $user -> setLastname('usr' . $i);
            $user -> setMail('user' . $i . '@gmail.fr' );
            $user -> setPassword('$2y$13$GRRf/zPuOP40Qjtd1Gk7Aei9WDMXSJVZUeOm0V.aUcCKr6Yl2HN.K');
            $user -> setAvatar('0.png');
            $user -> setPseudo('messi' . $i);
            $user -> setDateU(new \DateTime());
            $user -> setIsActiveU(1);
            $user -> setAge(new \DateTime('1998-01-22'));
            $manager -> persist($user);

            $member = new Member;
            $member -> setUser($user);
            $member -> setLevel(1);
            $manager -> persist($member);

            for ($j=0; $j <= 3; $j++) { 
                $game = new Game;
                $game -> setMember($member);
                $game -> setCategory($category);
                $game -> setName('Game' . $j);
                $game -> setDateG(new \DateTime('201' . $j . '-01-22'));
                $game -> setIsActive(1);
                $game -> setNbDownload(100);
                $game -> setDescriptionG('super jeux, ouahhhhhhhhhhhhhh il est trop cool, un suspens de dingue');
                $game -> setImg('0.png');
                $game -> setUrl('0.png');
                $game -> setPrix($j);
                $manager -> persist($game);

                for ($l=0; $l < 5; $l++) { 
                    $note = new Note;
                    $note -> setMember($member);
                    $note -> setGame($game);
                    $note -> setNote(rand(1,5));
                    $manager -> persist($note);
                }
                for ($m=0; $m < 15; $m++) { 
                    $comment = new Comment;
                    $comment -> setGame($game);
                    $comment -> setUser($user);
                    $comment -> setDescriptionC('comment blablablablablablabla balb akla' . $m);
                    $comment -> setDateC(new \DateTime());
                    $manager -> persist($comment);
                }
            }
        }

        $manager->flush();
    }
}

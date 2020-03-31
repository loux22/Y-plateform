<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function allGames(){
        $builder = $this -> createQueryBuilder('g');
        return $builder 
                -> select('g')
                -> distinct(true)
                -> getQuery()
                -> getResult();
         
    }
    public function lastGames() {
        //Afficher les 4 derniers jeux en date
        $builder = $this -> createQueryBuilder('g');
        return $builder 
                ->orderBy('g.date_g', 'DESC')
                ->setMaxResults(4)
                -> getQuery()
                -> getResult();
    }


    public function getGameList($member) {
        $builder = $this -> createQueryBuilder('m');
        return $builder 
            -> where('m.Member = :Member')
            -> setParameter('Member', $member)
            -> getQuery()
            -> getResult();
    } 

    public function findNbDownload($member)
    {
        return $this->createQueryBuilder('g')
        -> select("sum(g.nbDownload) as nbDownload")
        -> where('g.Member = :member')
        -> setParameter('member', $member)
        -> getQuery()
        -> getResult();
        
    }


    public function findNbGame($member)
    {
        return $this->createQueryBuilder('g')
        -> select("count(g.id) as nbGame")
        -> where('g.Member = :member')
        -> setParameter('member', $member)
        -> getQuery()
        -> getResult();
        
    }


    public function allNbDownload()
    {
        $builder = $this -> createQueryBuilder('g');
        return $builder
            -> select("sum(g.nbDownload) as nbDownload")
            -> getQuery()
            -> getResult();
    }


    public function allNbGames()
    {
        $builder = $this -> createQueryBuilder('g');
        return $builder
            -> select("count(g.id) as nbGames")
            -> getQuery()
            -> getResult();
    }

    public function last3Games()
    {
        $builder = $this -> createQueryBuilder('g');
        return $builder
            ->orderBy('g.date_g', 'DESC')
            ->setMaxResults(3)
            -> getQuery()
            -> getResult();
    }

    public function GamesCategory($category)
    {
        $builder = $this -> createQueryBuilder('g');
        return $builder
            -> leftJoin('g.category', 'c')
            -> where('c.id = :category')
            -> setParameter('category', $category)
            -> getQuery()
            -> getResult();
    }
//affiche les nouveau jeux
    public function NewGames()
    {
        $builder = $this -> createQueryBuilder('g');
        return $builder
            -> orderBy('g.date_g', 'DESC')
            -> setMaxResults(5)
            -> getQuery()
            -> getResult();
    }

    public function BetterSaleGames()
    {
        $builder = $this -> createQueryBuilder('g');
        return $builder
            -> orderBy('g.nbDownload', 'DESC')
            -> setMaxResults(5)
            -> getQuery()
            -> getResult();
    }

    public function searchGames($game)
    {
        $builder = $this -> createQueryBuilder('g');
        return $builder
            ->where('g.name LIKE :game')
            ->setParameter('game', $game.'%')
            -> getQuery()
            -> getResult();
    }



    // /**
    //  * @return Game[] Returns an array of Game objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Game
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

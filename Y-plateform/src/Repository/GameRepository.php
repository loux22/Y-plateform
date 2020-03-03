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

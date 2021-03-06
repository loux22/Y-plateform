<?php

namespace App\Repository;

use App\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function note($game){
        $builder = $this -> createQueryBuilder('n');
        return $builder 
                -> select("avg(n.note) as note")
                -> where('n.game = :game')
                -> setParameter('game', $game)
                -> getQuery()
                -> getResult();
         
    }

    public function noteJ($id_member){
        $builder = $this -> createQueryBuilder('n');
        return $builder 
                -> select("avg(n.note) as note")
                -> where('n.member = :member')
                -> setParameter('member', $id_member)
                -> getQuery()
                -> getResult();
         
    }

    public function AllNoteGame($game){
        $builder = $this -> createQueryBuilder('n');
        return $builder 
                -> where('n.game = :game')
                -> setParameter('game', $game)
                -> getQuery()
                -> getResult();
         
    }

    // /**
    //  * @return Note[] Returns an array of Note objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Note
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

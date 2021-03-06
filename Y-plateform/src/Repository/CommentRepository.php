<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */
    
    public function FindCommentGame($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.game = :game')
            ->setParameter('game', $value)
            ->orderBy('c.date_c', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function FindAllCommentGame($member)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.game', 'g')
            ->andWhere('g.Member = :member')
            ->setParameter('member', $member)
            ->getQuery()
            ->getResult()
        ;
    }
    
    public function NbAllCommentGame($member)
    {
        return $this->createQueryBuilder('c')
            -> select("count(c.id) as nbComment")
            ->leftJoin('c.game', 'g')
            ->andWhere('g.Member = :member')
            ->setParameter('member', $member)
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

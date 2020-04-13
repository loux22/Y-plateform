<?php

namespace App\Repository;

use App\Entity\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Member|null find($id, $lockMode = null, $lockVersion = null)
 * @method Member|null findOneBy(array $criteria, array $orderBy = null)
 * @method Member[]    findAll()
 * @method Member[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Member::class);
    }

    public function getUserProfil($id_user)
    {

        $builder = $this -> createQueryBuilder('m');
        return $builder 
                -> leftJoin('m.user', 'u')
                -> where('m.user = :user')
                -> setParameter('user', $id_user)
                -> getQuery()
                -> getResult();

    }

    public function allNbMembers()
    {
        $builder = $this -> createQueryBuilder('g');
        return $builder
            -> where('g.level = 1')
            -> select("count(g.id) as nbMembers")
            -> getQuery()
            -> getResult();
    }

    public function allMembers()
    {
        $builder = $this -> createQueryBuilder('a');
        return $builder
            -> leftJoin('a.user', 'u')
            -> where('a.user = :user')
            -> where('a.level = 1')
            -> getQuery()
            -> getResult();
    }

    public function allNbUsers()
    {
        $builder = $this -> createQueryBuilder('u');
        return $builder
            -> where('u.level = 0')
            -> select("count(u.id) as nbUsers")
            -> getQuery()
            -> getResult();
    }

    // /**
    //  * @return Member[] Returns an array of Member objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Member
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

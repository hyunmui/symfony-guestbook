<?php

namespace App\Repository;

use App\Entity\Coference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Coference|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coference|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coference[]    findAll()
 * @method Coference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coference::class);
    }

    // /**
    //  * @return Coference[] Returns an array of Coference objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Coference
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

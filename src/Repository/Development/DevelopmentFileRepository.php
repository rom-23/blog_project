<?php

namespace App\Repository\Development;

use App\Entity\Development\DevelopmentFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DevelopmentFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method DevelopmentFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method DevelopmentFile[]    findAll()
 * @method DevelopmentFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevelopmentFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DevelopmentFile::class);
    }

    // /**
    //  * @return File[] Returns an array of File objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?File
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

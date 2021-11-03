<?php

namespace App\Repository\Modelism;

use App\Entity\Modelism\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @param string|null $categoryName
     * @return array
     */
    public function search(?string $categoryName): array
    {
        return $this->createQueryBuilder('u')
                    ->where('u.name LIKE :category_name')
                    ->setParameter('category_name', "%$categoryName%")
                    ->setMaxResults(15)
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @return array
     */
    public function findAllCategories() : array
    {
        $sql = "
            SELECT
                partial e.{id, name}
            FROM App\Entity\Modelism\Category e
            ORDER BY e.name ASC
        ";
        return $this->getEntityManager()->createQuery($sql)->getResult();
    }
}

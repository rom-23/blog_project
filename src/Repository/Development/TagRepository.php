<?php

namespace App\Repository\Development;

use App\Entity\Development\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    public function search(string $tagName): array
    {
        return $this->createQueryBuilder('u')
                    ->where('u.name LIKE :tag_name')
                    ->setParameter('tag_name', "%$tagName%")
                    ->setMaxResults(15)
                    ->getQuery()
                    ->getResult();
    }

    public function findAllTags() :array
    {
        return $this->createQueryBuilder('u')
                    ->getQuery()
                    ->getResult();
    }
}

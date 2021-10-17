<?php

namespace App\Repository\Modelism;

use App\Entity\Modelism\Option;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Option|null find($id, $lockMode = null, $lockVersion = null)
 * @method Option|null findOneBy(array $criteria, array $orderBy = null)
 * @method Option[]    findAll()
 * @method Option[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Option::class);
    }

    public function findAllOptions()
    {
        $sql = "
            SELECT
                partial e.{id, name}
            FROM App\Entity\Modelism\Option e
            ORDER BY e.name ASC
        ";
        return $this->getEntityManager()->createQuery($sql)->getResult();
    }
}

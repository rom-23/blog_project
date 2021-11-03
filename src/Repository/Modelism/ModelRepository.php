<?php

namespace App\Repository\Modelism;

use App\Entity\Modelism\Model;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Model|null find($id, $lockMode = null, $lockVersion = null)
 * @method Model|null findOneBy(array $criteria, array $orderBy = null)
 * @method Model[]    findAll()
 * @method Model[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Model::class);
    }

    /**
     * @return array<array|null>
     */
    public function findModelPng(): array
    {
        $sql = "
            SELECT
                partial e.{id, name, description, price, thumbnail, createdAt},
                partial ljca.{id, name},
                partial ljim.{id, name}
            FROM App\Entity\Modelism\Model e
            LEFT JOIN e.images ljim
            LEFT JOIN e.categories ljca
            ORDER BY e.name ASC
        ";
        return $this->getEntityManager()->createQuery($sql)->getResult();
    }

    /**
     * @return Query
     */
    public function findDioramaPng(): Query
    {
        $sql        = "
            SELECT
                partial e.{id, name, description, price, thumbnail},
                partial ljim.{id, name},
                partial ljca.{id, name}
            FROM App\Entity\Modelism\Model e
            LEFT JOIN e.images ljim
            LEFT JOIN e.categories ljca
            WHERE ljca.name =:name
            ORDER BY e.name ASC
        ";
        $aParameter = ['name' => 'diorama',];
        return $this->getEntityManager()->createQuery($sql)->setParameters($aParameter);
    }

    /**
     * @return Query
     */
    public function find3dPng(): Query
    {
        $sql        = "
            SELECT
                partial e.{id, name, description, price, thumbnail},
                partial ljim.{id, name},
                partial ljca.{id, name}
            FROM App\Entity\Modelism\Model e
            LEFT JOIN e.images ljim
            LEFT JOIN e.categories ljca
            WHERE ljca.name =:name
            ORDER BY e.name ASC
        ";
        $aParameter = ['name' => '3d',];
        return $this->getEntityManager()->createQuery($sql)->setParameters($aParameter);
    }

    /**
     * @return Query
     */
    public function findCreationPng(): Query
    {
        $aParameter = [];
        $sql        = "
            SELECT
                partial e.{id, name, description, price, thumbnail},
                partial ljim.{id, name},
                partial ljca.{id, name}
            FROM App\Entity\Modelism\Model e
            LEFT JOIN e.images ljim
            LEFT JOIN e.categories ljca
            WHERE ljca.name =:name
            ORDER BY e.name ASC
        ";
        $aParameter = ['name' => 'original creation',];
        return $this->getEntityManager()->createQuery($sql)->setParameters($aParameter);
    }

    /**
     * @param string $name
     * @return Query
     */
    public function findModelkitPng(string $name): Query
    {
        $aParameter = [];
        $sql        = "
            SELECT
                partial e.{id, name, description, price, thumbnail},
                partial ljim.{id, name},
                partial ljca.{id, name}
            FROM App\Entity\Modelism\Model e
            LEFT JOIN e.images ljim
            LEFT JOIN e.categories ljca
            WHERE ljca.name =:name
            ORDER BY e.name ASC
        ";
        $aParameter = ['name' => strtolower($name),];
        return $this->getEntityManager()->createQuery($sql)->setParameters($aParameter);
    }

    /**
     * @return array
     */
    public function findAllModelkit(): array
    {
        return $this->createQueryBuilder('p')->getQuery()->getResult();
    }
}

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
     * @return array
     */
    public function findAllModels(): array
    {
        $sql = "
            SELECT
                partial e.{id, name, description, price, thumbnail, createdAt},
                partial ljim.{id, name},
                partial ljca.{id, name},
                partial ljop.{id, name},
                partial ljopi.{id, vote, comment, createdAt}
            FROM App\Entity\Modelism\Model e
            LEFT JOIN e.images ljim
            LEFT JOIN e.categories ljca
            LEFT JOIN e.options ljop
            LEFT JOIN e.opinions ljopi
            WHERE ljca.name =:name
            ORDER BY e.name ASC              
        ";
        $aParameter = ['name' => 'Model kit'];
        return $this->getEntityManager()->createQuery($sql)->setParameters($aParameter)->getResult();
    }

    /**
     * @return array
     */
    public function findAllDioramas(): array
    {
        $sql = "
      SELECT
                partial e.{id, name, description, price, thumbnail, createdAt},
                partial ljim.{id, name},
                partial ljca.{id, name},
                partial ljop.{id, name},
                partial ljopi.{id, vote, comment, createdAt}
            FROM App\Entity\Modelism\Model e
            LEFT JOIN e.images ljim
            LEFT JOIN e.categories ljca
            LEFT JOIN e.options ljop
            LEFT JOIN e.opinions ljopi
            WHERE ljca.name =:name
            ORDER BY e.name ASC              
        ";
        $aParameter = ['name' => 'Diorama'];
        return $this->getEntityManager()->createQuery($sql)->setParameters($aParameter)->getResult();
    }

    /**
     * @return array
     */
    public function findAll3d(): array
    {
        $sql = "
            SELECT
                partial e.{id, name, description, price, thumbnail, createdAt},
                partial ljim.{id, name},
                partial ljca.{id, name},
                partial ljop.{id, name},
                partial ljopi.{id, vote, comment, createdAt}
            FROM App\Entity\Modelism\Model e
            LEFT JOIN e.images ljim
            LEFT JOIN e.categories ljca
            LEFT JOIN e.options ljop
            LEFT JOIN e.opinions ljopi
            WHERE ljca.name =:name
            ORDER BY e.name ASC
        ";
        $aParameter = ['name' => '3D print'];
        return $this->getEntityManager()->createQuery($sql)->setParameters($aParameter)->getResult();
    }

    /**
     * @return array
     */
    public function findAllOriginalCreations(): array
    {
        $aParameter = [];
        $sql        = "
            SELECT
                partial e.{id, name, description, price, thumbnail},
                partial ljim.{id, name},
                partial ljca.{id, name},
                partial ljop.{id, name},
                partial ljopi.{id, vote, comment, createdAt}
            FROM App\Entity\Modelism\Model e
            LEFT JOIN e.images ljim
            LEFT JOIN e.categories ljca
            LEFT JOIN e.options ljop
            LEFT JOIN e.opinions ljopi
            WHERE ljca.name =:name
            ORDER BY e.name ASC
        ";
        $aParameter = ['name' => 'original creation'];
        return $this->getEntityManager()->createQuery($sql)->setParameters($aParameter)->getResult();
    }

    /**
     * @param int $id
     * @return array
     */
    public function findModelsByCategory(int $id): array
    {
        $aParameter = [];
        $sql        = "
            SELECT
                partial e.{id, name, description, price, thumbnail, createdAt},
                partial ljim.{id, name},
                partial ljca.{id, name},
                partial ljop.{id, name},
                partial ljopi.{id, vote, comment, createdAt}
            FROM App\Entity\Modelism\Model e
            LEFT JOIN e.images ljim
            LEFT JOIN e.categories ljca
            LEFT JOIN e.options ljop
            LEFT JOIN e.opinions ljopi
            WHERE ljca.id =:id
            ORDER BY e.name ASC
        ";
        $aParameter = ['id' => $id];
        return $this->getEntityManager()->createQuery($sql)->setParameters($aParameter)->getResult();
    }

    /**
     * @return Query
     */
    public function paginateModels(): Query
    {
        $sql = "
                SELECT
                  partial e.{id,name,thumbnail, description, price, createdAt, updated_at},
                  partial cat.{id, name},
                  partial opt.{id, name},
                  partial img.{id, name},
                  partial opi.{id, vote, comment, createdAt}
            FROM App\Entity\Modelism\Model e
            LEFT JOIN e.categories cat
            LEFT JOIN e.options opt
            LEFT JOIN e.images img
            LEFT JOIN e.opinions opi
        ";
        return $this->getEntityManager()->createQuery($sql);
    }
}

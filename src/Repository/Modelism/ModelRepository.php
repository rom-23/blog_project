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

    /** findModelPng
     */
    public function findModelPng()
    {
        $sql = "
            SELECT
                partial e.{id, name, description, price, filename, original},
                partial ljca.{id, name},
                partial ljim.{id, path}
            FROM App\Entity\Modelism\Model e
            LEFT JOIN e.images ljim
            LEFT JOIN e.categories ljca
            ORDER BY e.name ASC
        ";
        return $this->getEntityManager()->createQuery($sql)->getResult();
    }

    /** findDioramaPng
     * @return Query
     */
    public function findDioramaPng(): Query
    {
        $sql = "
            SELECT
                partial e.{id, name, description, price, filename},
                partial ljim.{id, path},
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

    /** find3dPng
     * @return Query
     */
    public function find3dPng(): Query
    {
        $sql = "
            SELECT
                partial e.{id, name, description, price, filename},
                partial ljim.{id, path},
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

    /** findCreationPng
     * @return Query
     */
    public function findCreationPng(): Query
    {
        $sql = "
            SELECT
                partial e.{id, name, description, price, filename},
                partial ljim.{id, path},
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

    /** findModelkitPng for nav menu
     * @return Query
     */
    public function findModelkitPng($name): Query
    {
        $sql = "
            SELECT
                partial e.{id, name, description, price, filename, original},
                partial ljim.{id, path},
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
     * @return Model[] Returns an array of Last Model objects
     */
    public function findAllModelkit(): array
    {
        return $this -> createQueryBuilder( 'p' )-> getQuery()-> getResult();
    }
}

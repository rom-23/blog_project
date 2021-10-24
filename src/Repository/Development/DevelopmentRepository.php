<?php

namespace App\Repository\Development;

use App\Entity\Development\Development;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use http\QueryString;

/**
 * @method Development|null find($id, $lockMode = null, $lockVersion = null)
 * @method Development|null findOneBy(array $criteria, array $orderBy = null)
 * @method Development[]    findAll()
 * @method Development[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevelopmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Development::class);
    }

    /**
     * @return Query
     */
    public function paginateDevelopments(): Query
    {
        $sql = "
                SELECT
                  partial e.{id,title,content, createdAt, updatedAt},
                  partial sect.{id, title},
                  partial fil.{id},
                  partial pos.{id, title},
                  partial not.{id, title},
                  partial ta.{id, name}
            FROM App\Entity\Development\Development e
            LEFT JOIN e.section sect
            LEFT JOIN e.files fil
            LEFT JOIN e.posts pos
            LEFT JOIN e.notes not
            LEFT JOIN e.tags ta
        ";
        return $this->getEntityManager()->createQuery($sql);
    }

    /**
     * @param int $page
     * @param int $limit
     * @return Paginator
     */
    public function findPaginatedDevelopment(int $page, int $limit): Paginator
    {
        $sql = "
                SELECT
                  partial e.{id,title,content, createdAt, updatedAt},
                  partial sect.{id, title},
                  partial fil.{id},
                  partial pos.{id, title},
                  partial not.{id, title},
                  partial ta.{id, name}
            FROM App\Entity\Development\Development e
            LEFT JOIN e.section sect
            LEFT JOIN e.files fil
            LEFT JOIN e.posts pos
            LEFT JOIN e.notes not
            LEFT JOIN e.tags ta
        ";

        return new Paginator(
            $this->getEntityManager()->createQuery($sql)
                 ->setMaxResults($limit)
                 ->setFirstResult(($page - 1) * $limit));
    }

    /**
     * @param mixed $title
     * @return array
     */
    public
    function findDocBySection(mixed $title): array
    {
        $aParameter = [];
        $sql        = "
                SELECT
                  partial e.{id,title,content},
                  partial ljco.{id, title}
            FROM App\Entity\Development\Development e
            INNER JOIN e.section ljco
            WHERE ljco.id = :sectionId
        ";
        $aParameter = [
            'sectionId' => $title
        ];
        return $this->getEntityManager()->createQuery($sql)->setParameters($aParameter)->getResult();
    }

    /**
     * @param mixed $words
     * @return mixed
     */
    public
    function searchDevelopment(mixed $words): array
    {
        $aParameter = [];
        $sql        = "
                SELECT
                  partial e.{id,title,content},
                  partial ljco.{id, title}
            FROM App\Entity\Development\Development e
            INNER JOIN e.section ljco
        ";
        if ($words != null) {
            $sql        .= "
                    WHERE MATCH_AGAINST(e.title, e.content) AGAINST(:words boolean) > 0
                    OR MATCH_AGAINST(ljco.title) AGAINST(:words boolean) > 0
            ";
            $aParameter = [
                'words' => $words
            ];
        }
        return $this->getEntityManager()->createQuery($sql)->setParameters($aParameter)->getResult();
    }
}

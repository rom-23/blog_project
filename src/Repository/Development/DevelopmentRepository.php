<?php

namespace App\Repository\Development;

use App\Entity\Development\Development;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function findDocBySection($title)
    {
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

    public function searchDevelopment($words)
    {
        $sql = "
                SELECT
                  partial e.{id,title,content},
                  partial ljco.{id, title}
            FROM App\Entity\Development\Development e
            INNER JOIN e.section ljco
        ";
        if ($words != null) {
            $sql .= "
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

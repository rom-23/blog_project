<?php

namespace App\Service;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;

class ManagePaginator
{
    /**
     * @param Query|QueryBuilder $query
     * @param int $page
     * @param int $limit
     * @return Paginator
     */
    public function paginate(Query|QueryBuilder $query, int $page, int $limit): Paginator
    {
        $paginator = new Paginator($query);
        $paginator
            ->getQuery()
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit);

        return $paginator;
    }

    /**
     * @param int $page
     * @param Paginator $object
     * @return array
     */
    public function rangePaginator(int $page, Paginator $paginator): array
    {
        return range(
            max($page - 3, 1),
            min($page + 3, $this->lastPage($paginator))
        );
    }

    /**
     * @param Paginator $paginator
     * @return int
     */
    public function lastPage(Paginator $paginator): int
    {
        return ceil($paginator->count() / $paginator->getQuery()->getMaxResults());
    }

    /**
     * @param Paginator $paginator
     * @return int
     */
    public function total(Paginator $paginator): int
    {
        return $paginator->count();
    }

    /**
     * @param Paginator $paginator
     * @return bool
     * @throws Exception
     */
    public function currentPageHasNoResult(Paginator $paginator): bool
    {
        return !$paginator->getIterator()->count();
    }
}

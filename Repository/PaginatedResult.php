<?php
/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 08.06.20
 * Time: 11:42
 */

namespace Shopguardians\Repository;


use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Shopguardians\ControllerShared\PaginationData;

class PaginatedResult
{

    /**
     * @param $paginationData PaginationData
     * @param $query Query
     * @return array
     */
    public static function get($paginationData, $query)
    {
        $paginator = new Paginator($query);
        $totalCount = $paginator->count();
        $pagesCount = (int)ceil($totalCount / $paginationData->getPerPage());

        $paginator->getQuery()
            ->setFirstResult($paginationData->getOffset())
            ->setMaxResults($paginationData->getPerPage());

        return [
            'result' => iterator_to_array($paginator),
            'page' => $paginationData->getPage(),
            'perPage' => $paginationData->getPerPage(),
            'totalCount' => $totalCount,
            'pagesCount' => $pagesCount,
        ];
    }


}
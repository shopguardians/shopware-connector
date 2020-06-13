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
        $paginationData->setFromPaginator($paginator);
        $paginator->getQuery()
            ->setFirstResult($paginationData->getOffset())
            ->setMaxResults($paginationData->getPerPage());
        return [
            'pagination' => $paginationData->getData(),
            'result' => iterator_to_array($paginator),
        ];
    }


}
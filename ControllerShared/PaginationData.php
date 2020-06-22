<?php
/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 12.06.20
 * Time: 14:48
 */

namespace Shopguardians\ControllerShared;

class PaginationData
{

    private $data = [];

    /**
     * @var Integer | null
     */
    private $page;

    /**
     * @var Integer | null
     */
    private $perPage;

    /**
     * @var Integer | null
     */
    private $totalPages;

    /**
     * @var int | null
     */
    private $totalCount;

    /**
     * @return Integer|null
     */
    public function getPage()
    {
        return isset($this->data['page']) ? $this->data['page'] : 0;
    }

    /**
     * @param Integer|null $page
     */
    public function setPage($page)
    {
        $this->data['page'] = $page;
    }

    /**
     * @return Integer|null
     */
    public function getPerPage()
    {
        return isset($this->data['perPage']) ? $this->data['perPage'] : 100;
    }

    public function getOffset()
    {
        $page = $this->getPage() - 1;
        if ($page < 0) $page = 0;

        return ($page * $this->getPerPage());
    }

    /**
     * @param Integer|null $perPage
     */
    public function setPerPage($perPage)
    {
        $this->data['perPage'] = $perPage;
    }

    /**
     * @return Integer|null
     */
    public function getTotalPages()
    {
        return isset($this->data['totalPages']) ? $this->data['totalPages'] : null;
    }

    /**
     * @param Integer|null $totalPages
     */
    public function setPagesCount($totalPages)
    {
        $this->data['pagesCount'] = $totalPages;
    }

    public function setPagesCountFromTotalCount($totalCount)
    {
        $this->data['pagesCount'] = self::calcTotalPages($totalCount, $this->getPerPage());
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int|null
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @param int|null $totalCount
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;
    }


    public static function calcTotalPages($totalCount, $perPage)
    {
        return (int) ceil($totalCount / $perPage);
    }

    /**
     * @param \Doctrine\ORM\Tools\Pagination\Paginator $paginator
     */
    public function setFromPaginator($paginator)
    {
        $this->setTotalCount($paginator->count());
        $this->setPagesCount(
            (int)ceil($this->getTotalCount() / $this->getPerPage())
        );
    }


}
<?php
/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 04.06.20
 * Time: 17:49
 */

namespace Shopguardians\Repository;


use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Shopguardians\ControllerShared\PaginationData;
use Shopguardians\Model\ShopguardiansArticleSerializer;
use Shopware\Models\Article\Detail;

class ArticleDetailRepository extends AbstractBaseRepository
{
    static function get()
    {
        return new self();
    }

    /**
     * @param $query Query
     * @param $paginationData PaginationData
     * @param int $hydrate
     * @return array
     */
    public function paginateQuery($query,$paginationData, $hydrate = AbstractQuery::HYDRATE_ARRAY)
    {
        $query->setHydrationMode($hydrate);
        $paginationResult = PaginatedResult::get(
            $paginationData,
            $query
        );
        if ($hydrate === AbstractQuery::HYDRATE_ARRAY) {
            $paginationResult['result'] = ShopguardiansArticleSerializer::fromArrayHydratedDetailCollection($paginationResult['result'] ?? []);
        }
        return $paginationResult;
    }

    public function getWithoutStockQueryBuilder()
    {
        return $this->getQueryBuilder()
            ->select('detail', 'article')
            ->from(Detail::class, 'detail')
            ->join('detail.article', 'article')
            ->addSelect('images')
            ->leftJoin('article.images', 'images')
            ->addSelect('media')
            ->leftJoin('images.media', 'media')
            ->where('article.active = 1')
            ->andwhere('detail.active = 1')
            ->andWhere('detail.inStock < 1');
    }

    /**
     * @param $paginationData PaginationData
     * @return array
     */
    public function getPaginatedWithoutStock($paginationData)
    {
        $query = $this->getWithoutStockQueryBuilder()->getQuery();
        return $this->paginateQuery($query, $paginationData);
    }

    public function getWithoutImagesQueryBuilder()
    {
        return $this->getQueryBuilder()
            ->select('detail')
            ->from(Detail::class, 'detail')
            ->join('detail.article', 'article')
            ->addSelect('article')
            ->leftJoin('article.images', 'images')
            ->where('article.active = 1')
            ->andWhere('images.id is null');
    }

    /**
     * @param $paginationData PaginationData
     * @return array
     */
    public function getPaginatedWithoutImages($paginationData)
    {
        $query = $this->getWithoutImagesQueryBuilder()->getQuery();
        return $this->paginateQuery($query, $paginationData);
    }

    public function getWithoutDescriptionQueryBuilder()
    {
        $qb =  $this->getQueryBuilder();
        return $qb->select('detail', 'article')
            ->from(Detail::class, 'detail')
            ->addSelect('article')
            ->join('detail.article', 'article')
            ->addSelect('images')
            ->leftJoin('article.images', 'images')
            ->addSelect('media')
            ->leftJoin('images.media', 'media')
            ->where('article.active = 1')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->isNull('article.descriptionLong'),
                    $qb->expr()->eq('article.descriptionLong', "''")
                )
            );
    }

    /**
     * @param $paginationData PaginationData
     * @return array
     */
    public function getPaginatedWithoutDescription($paginationData)
    {
        $query = $this->getWithoutDescriptionQueryBuilder()->getQuery();
        return $this->paginateQuery($query,$paginationData);
    }

    public function getWithoutCategoriesQueryBuilder()
    {
        $qb =  $this->getQueryBuilder();
        return $qb->select('detail')
            ->from(Detail::class, 'detail')
            ->addSelect('article')
            ->join('detail.article', 'article')
            ->addSelect('images')
            ->leftJoin('article.images', 'images')
            ->leftJoin('article.categories', 'categories')
            ->addSelect('media')
            ->leftJoin('images.media', 'media')
            ->where('article.active = 1')
            ->andWhere('categories.id is null');
    }

    public function getPaginatedWithoutCategories($paginationData)
    {
        $query = $this->getWithoutCategoriesQueryBuilder()->getQuery();
        return $this->paginateQuery($query, $paginationData);
    }

}
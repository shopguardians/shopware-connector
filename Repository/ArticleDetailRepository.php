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
use Shopware\Components\Model\QueryBuilder;
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

    /**
     * @param $queryBuilder \Doctrine\ORM\QueryBuilder
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function addJoinWithCurrentShopCategory($queryBuilder)
    {
        return $queryBuilder
            ->join('article.allCategories', 'allCategories')
            ->where('allCategories.id = :shopCategoryId')
            ->setParameter('shopCategoryId', Shopware()->Shop()->getCategory()->getId());
    }

    /**
     * @param $queryBuilder \Doctrine\ORM\QueryBuilder
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function addDefaultSelectAndJoins($queryBuilder)
    {
        return $queryBuilder->select('detail')
            ->from(Detail::class, 'detail')
            ->join('detail.article', 'article')
            ->addSelect('article');
    }

    /**
     * @param $queryBuilder \Doctrine\ORM\QueryBuilder
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function addWhereOnlyActive($queryBuilder)
    {
        return $queryBuilder
            ->andWhere('article.active = 1')
            ->andWhere('detail.active = 1');
    }

    /**
     * @param $queryBuilder \Doctrine\ORM\QueryBuilder
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function addJoinAndSelectImages($queryBuilder)
    {
        return $queryBuilder
            ->addSelect('images')
            ->leftJoin('article.images', 'images');
    }

    /**
     * @param bool[] || null $options
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getDefaultQueryBuilder(
        $options = [
            'currentShopOnly' => true, 'onlyActive' => true
        ]
    )
    {
        $qb = $this->getQueryBuilder();
        $qb = $this->addDefaultSelectAndJoins($qb);
        if ($options['currentShopOnly'] ?? true) {
            $qb = $this->addJoinWithCurrentShopCategory($qb);
        }
        $qb = $this->addJoinAndSelectImages($qb);
        if ($options['onlyActive'] ?? true) {
            $qb = $this->addWhereOnlyActive($qb);
        }
        return $qb;
    }

    public function getWithoutStockQueryBuilder()
    {
        return $this->getDefaultQueryBuilder()
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
        return $this->getDefaultQueryBuilder()
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
        $qb = $this->getDefaultQueryBuilder();
        return $qb->andWhere(
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
        $qb =  $this->getDefaultQueryBuilder(['currentShopOnly' => false]);
        return $qb
            ->leftJoin('article.categories', 'categories')
            ->andWhere('categories.id is null');
    }

    public function getPaginatedWithoutCategories($paginationData)
    {
        $query = $this->getWithoutCategoriesQueryBuilder()->getQuery();
        return $this->paginateQuery($query, $paginationData);
    }

}
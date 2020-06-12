<?php
/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 04.06.20
 * Time: 17:51
 */

namespace Shopguardians\Repository;


use Doctrine\DBAL\Connection;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Model\QueryBuilder;

abstract class AbstractBaseRepository
{
    abstract static function get();
    /**
     * @var ModelManager
     */
    protected $entityManager;

    public function __construct()
    {
        $this->entityManager = Shopware()->Models();
    }

    public function getConnection(): Connection
    {
        return $this->entityManager->getConnection();
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->entityManager->createQueryBuilder();
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getSqlQueryBuilder()
    {
        return Shopware()->Models()->getConnection()->createQueryBuilder();
    }
}
<?php
/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 04.06.20
 * Time: 17:47
 */

class Shopware_Controllers_Frontend_ShopguardiansApiDataQuality
    extends \Shopguardians\ControllerShared\ShopguardiansApiBaseController
    implements \Shopware\Components\CSRFWhitelistAware
{

    public function getWhitelistedCSRFActions()
    {
        return [
            'articlesWithoutStock', 'articlesWithoutImage',
            'articlesWithoutDescription', 'articlesWithoutCategory'
        ];
    }

    public function articlesWithoutStockAction()
    {
        $this->setNoRender();
        $paginatedResult = \Shopguardians\Repository\ArticleDetailRepository::get()->getPaginatedWithoutStock(
            $this->getPaginationData()
        );
        $this->renderJson($paginatedResult);
    }

    public function articlesWithoutImageAction()
    {
        $this->setNoRender();
        $paginatedResult = \Shopguardians\Repository\ArticleDetailRepository::get()->getPaginatedWithoutImages(
            $this->getPaginationData()
        );
        $this->renderJson($paginatedResult);
    }

    public function articlesWithoutDescriptionAction()
    {
        $this->setNoRender();
        $paginatedResult = \Shopguardians\Repository\ArticleDetailRepository::get()->getPaginatedWithoutDescription(
            $this->getPaginationData()
        );
        $this->renderJson($paginatedResult);
    }

    public function articlesWithoutCategoryAction()
    {
        $this->setNoRender();
        $paginatedResult = \Shopguardians\Repository\ArticleDetailRepository::get()->getPaginatedWithoutCategories(
            $this->getPaginationData()
        );
        $this->renderJson($paginatedResult);
    }

}
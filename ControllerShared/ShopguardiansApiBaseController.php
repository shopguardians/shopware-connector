<?php
/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 04.06.20
 * Time: 17:25
 */

namespace Shopguardians\ControllerShared;


use Enlight_Controller_Action;
use Shopguardians\Configuration\ConfigurationManager;
use Shopware\Components\CSRFWhitelistAware;

class ShopguardiansApiBaseController extends Enlight_Controller_Action
{
    use ControllerTrait;

    public function __construct($request = null, $response = null)
    {
        $this->handleOptionsPreflightRequest();
        $this->authorize();
        parent::__construct($request, $response);
    }


    /**
     * @return bool
     * @throws \Exception
     */
    public function authorize()
    {
        $apiKey = ConfigurationManager::getApiKey();
        $apiKeyToTest = $_SERVER['HTTP_X_API_KEY'] ?? null;
        if (!$apiKeyToTest) {
            throw new \Exception('Unauthorized');
        }
        if ($apiKeyToTest !== $apiKey) {
            throw new \Exception('Unauthorized');
        }
        return true;
    }

    /**
     * @return PaginationData
     */
    public function getPaginationData()
    {
        $pagination = new PaginationData();
        $perPage = Shopware()->Front()->Request()->getParam('limit', 100);
        $perPage = intval($perPage);
        $pagination->setPerPage($perPage);

        $page = Shopware()->Front()->Request()->getParam('page', 0);
        $page = intval($page);
        $pagination->setPage($page);

        return $pagination;
    }

}
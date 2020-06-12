<?php

use Shopware\Components\CSRFWhitelistAware;

/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 04.06.20
 * Time: 15:59
 */

class Shopware_Controllers_Frontend_ShopguardiansApi extends \Shopguardians\ControllerShared\ShopguardiansApiBaseController implements CSRFWhitelistAware
{

    public function getWhitelistedCSRFActions()
    {
        return ['index', 'foo'];
    }

    public function indexAction() {
        $this->setNoRender();
        echo json_encode(["hello" => "world"]);
    }

    public function fooAction()
    {
        echo json_encode(["hello" => "worlds"]);
        exit();
    }

}
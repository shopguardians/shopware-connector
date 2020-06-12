<?php
/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 09.06.20
 * Time: 14:16
 */

class Shopware_Controllers_Frontend_ShopguardiansApiCustomer
    extends \Shopguardians\ControllerShared\ShopguardiansApiBaseController
    implements \Shopware\Components\CSRFWhitelistAware
{

    public function getWhitelistedCSRFActions()
    {
        return ['customerStats'];
    }

    public function customerStatsAction()
    {
        $this->setNoRender();
        $result = [
            'customers_today' => \Shopguardians\Repository\CustomerRepository::get()->getCustomersRegisteredTodayCount(),
            'customers_total' => \Shopguardians\Repository\CustomerRepository::get()->getCustomersTotalCount(),
            'newsletter_today' => \Shopguardians\Repository\CustomerRepository::get()->getNewsletterRegistrationsTodayCount(),
            'newsletter_total' => \Shopguardians\Repository\CustomerRepository::get()->getNewsletterTotalCount()
        ];
        $this->renderJson($result);
    }

}
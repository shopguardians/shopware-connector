<?php

use Shopguardians\Repository\OrderRepository;

/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 09.06.20
 * Time: 09:57
 */

class Shopware_Controllers_Frontend_ShopguardiansApiOrder
    extends \Shopguardians\ControllerShared\ShopguardiansApiBaseController
    implements \Shopware\Components\CSRFWhitelistAware
{

    public function getWhitelistedCSRFActions()
    {
        return [
            'orderStats', 'heuristicSeedData', 'newestOrderDate',
            'newestOrderDatesByPaymentMethods', 'newestOrderDateForPayment',
            'newestOrderDateForPaymentMethod',
        ];
    }

    public function orderStatsAction()
    {
        $this->setNoRender();
        $result = \Shopguardians\Repository\OrderRepository::get()->getOrderStats();
        $this->renderJson($result);
        die();
    }

    public function heuristicSeedDataAction()
    {
        $this->setNoRender();
        $result = [
            'usualWorkingHours' => \Shopguardians\Order\OrderHeuristic::getUsualWorkingHours(),
            'averageOrderDistancesByWeekdayAndPaymentMethods' => \Shopguardians\Order\OrderHeuristic::getOrderDistancesByWeekdayAndPaymentMethods(),
            'averageOrderDistancesByWeekday' => \Shopguardians\Order\OrderHeuristic::getOrderDistancesByWeekday(),
        ];
        $this->renderJson($result);
    }

    public function newestOrderDateAction()
    {
        $this->setNoRender();
        $result = OrderRepository::get()->getLastOrderDate();
        $result = $result[0]['lastOrderDate'] ?? null;
        $this->renderJson($result);
    }

    public function newestOrderDateForPaymentMethodAction()
    {
        $this->setNoRender();
        $paymentMethod = Shopware()->Front()->Request()->getParam('paymentMethod');
        if (!$paymentMethod) {
            throw new Exception('IllegalState: invalid request');
        }
        $payment = \Shopguardians\Repository\PaymentRepository::get()->findByName($paymentMethod);
        if (!$payment) {
            throw new Exception('IllegalState: no payment found');
        }
        $result = OrderRepository::get()->getLastOrderDate($payment->getId());
        $result = $result[0]['lastOrderDate'] ?? null;
        $this->renderJson($result);
    }

    public function newestOrderDatesByPaymentMethodsAction()
    {
        $this->setNoRender();
        $payments = \Shopguardians\Repository\PaymentRepository::get()->getActivelyUsedPaymentMethodsDaysAgo(
            \Shopguardians\Configuration\ConfigurationManager::getPaymentMethodActivitySpanDays()
        );
        $result = OrderRepository::get()->getNewestOrdersByPaymentType($payments);
        $this->renderJson($result);
    }

}
<?php
/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 10.06.20
 * Time: 11:59
 */

namespace Shopguardians\Repository;


use Shopware\Models\Payment\Payment;

class PaymentRepository extends AbstractBaseRepository
{
    static function get()
    {
        return new self();
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository
     */
    public function getShopwareRepository()
    {
        return Shopware()->Models()->getRepository(Payment::class);
    }

    /**
     * @return Payment[]
     */
    public function getAll()
    {
        return $this->getShopwareRepository()->findAll();
    }

    /**
     * @param $paymentMethodName
     * @return Payment | null
     */
    function findByName($paymentMethodName)
    {
        return $this->getShopwareRepository()->findOneBy(['name' => $paymentMethodName]);
    }

    /**
     * @param null | int $daysAgo
     * @return array|object[]
     */
    function getActivelyUsedPaymentMethodsDaysAgo($daysAgo)
    {
        $qb = $this->getSqlQueryBuilder()
            ->select(
                'payments.id as id'
            )
            ->from('s_order', 'orders')
            ->leftJoin('orders', 's_core_paymentmeans', 'payments', 'payments.id = orders.paymentID')
            ->where('orders.ordertime >= now() - interval :daysAgo day')
            ->setParameter('daysAgo', $daysAgo)
            ->andWhere('payments.id is not null')
            ->groupBy('orders.paymentId');

        $result = $qb->execute()->fetchAll();
        $ids = array_map(function($it){
            return $it['id'] ?? null;
        }, $result);
        $ids = array_values(array_filter($ids));
        return $this->getShopwareRepository()->findBy(['id' => $ids]);
    }

}
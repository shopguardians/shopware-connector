<?php
/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 09.06.20
 * Time: 10:00
 */

namespace Shopguardians\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;
use Shopguardians\Shared\TimeUtils;
use Shopware\Models\Payment\Payment;

class OrderRepository extends AbstractBaseRepository
{
    /**
     * @return OrderRepository
     */
    static function get()
    {
        return new self();
    }

    /**
     * @param $queryBuilder QueryBuilder
     * @return mixed
     */
    public function addInRangeTodayCondition($queryBuilder)
    {
        $fromDate = TimeUtils::getBeginningOfToday();
        $endDate = TimeUtils::getBeginningOfTomorrow();
        $queryBuilder->where('s_order.ordertime > :startDate')
            ->andWhere('s_order.ordertime < :endDate')
            ->setParameter('startDate', $fromDate, Type::DATE)
            ->setParameter('endDate', $endDate, Type::DATE);
        return $queryBuilder;
    }

    public function getOrdersTodayCount()
    {
        $qb = $this->getSqlQueryBuilder()
            ->select('count(s_order.id) as total')
            ->from('s_order');

        $qb = $this->addInRangeTodayCondition($qb);
        $result = $qb->execute()->fetchAll();
        return $result[0]['total'] ?? 0;
    }

    public function getOrderTodayRevenue()
    {
        $qb = $this->getSqlQueryBuilder()
            ->select('sum(s_order.invoice_amount) as total')
            ->from('s_order');
        $qb = $this->addInRangeTodayCondition($qb);
        $result = $qb->execute()->fetchAll();
        return $result[0]['total'] ?? 0;
    }

    public function getRevenueTotal()
    {
        $qb = $this->getSqlQueryBuilder()
            ->select('sum(s_order.invoice_amount) as total')
            ->from('s_order');
        $result = $qb->execute()->fetchAll();
        return $result[0]['total'] ?? 0;
    }

    public function getOrdersTotalCount()
    {
        $qb = $this->getSqlQueryBuilder()
            ->select('count(s_order.id) as total')
            ->from('s_order');
        $result = $qb->execute()->fetchAll();
        return $result[0]['total'] ?? 0;
    }

    /**
     * @return array
     */
    public function getOrderStats()
    {
        return [
            'orders_today' => $this->getOrdersTodayCount(),
            'orders_total' => $this->getOrdersTotalCount(),
            'revenue_today' => $this->getOrderTodayRevenue(),
            'revenue_total' => $this->getRevenueTotal()
        ];
    }

    public function getAverageOrdersGroupedByHours()
    {
        $qb = $this->getSqlQueryBuilder()
            ->select('count(id) as numOrders', 'hour(ordertime) as hour')
            ->from('s_order')
            ->groupBy('hour(s_order.ordertime)');
        return $qb->execute()->fetchAll();
    }


    public function getAvgMinutesBetweenOrdersInDateRangeByWeekday($startDate, $endDate, $paymentId = null)
    {
        $qb = $this->getSqlQueryBuilder()
            ->select(
                'TIMESTAMPDIFF(MINUTE, MIN(s_order.ordertime), MAX(s_order.ordertime) )'
                   . ' / (COUNT(DISTINCT(s_order.ordertime)) -1) AS avgMinutes',
                'WEEKDAY(s_order.ordertime) AS weekdayNumber'
            )
            ->from('s_order')
            ->where('s_order.ordertime > :startDate')
            ->setParameter('startDate', $startDate, Type::DATE)
            ->andWhere('s_order.ordertime < :endDate')
            ->setParameter('endDate', $endDate, TYPE::DATE)
            ->groupBy('weekday(s_order.ordertime)');

        if ($paymentId) {
            $qb->andWhere('s_order.paymentID = :paymentId')
                ->setParameter('paymentId', $paymentId);
        }

        return $qb->execute()->fetchAll();
    }

    /**
     * @param null | int $paymentId
     * @return array
     */
    public function getLastOrderDate($paymentId = null)
    {
        $qb = $this->getSqlQueryBuilder()
            ->select('MAX(s_order.ordertime) as lastOrderDate')
            ->from('s_order');
        if ($paymentId) {
            $qb->where('paymentID = :paymentId')
                ->setParameter('paymentId', $paymentId);
        }
        return $qb->execute()->fetchAll();
    }

    /**
     * @param $payments Payment[]
     */
    public function getNewestOrdersByPaymentType($payments)
    {
        $ids = array_map(function($it) {
            return $it->getId();
        }, $payments);
        $ids = array_values($ids);
        $qb = $this->getSqlQueryBuilder()
            ->select(
                'payments.name as paymentMethod',
                'MAX(orders.ordertime) as orderDate',
                'payments.description as description'
            )
            ->from('s_core_paymentmeans', 'payments')
            ->join('payments', 's_order', 'orders', 'orders.paymentID = payments.id')
            ->where('payments.id in (:ids)')
            ->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY)
            ->groupBy('paymentMethod');
        return $qb->execute()->fetchAll();
    }

}
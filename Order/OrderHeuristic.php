<?php
/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 10.06.20
 * Time: 10:57
 */
namespace Shopguardians\Order;

use Shopguardians\Configuration\ConfigurationManager;
use Shopguardians\Repository\OrderRepository;
use Shopguardians\Repository\PaymentRepository;
use Shopguardians\Shared\TimeUtils;
use Shopware\Models\Payment\Payment;

class OrderHeuristic
{

    public static function getUsualWorkingHours()
    {
        $numOrdersGroupedByHours = OrderRepository::get()->getAverageOrdersGroupedByHours();
        $totalOrders = 0;
        foreach ($numOrdersGroupedByHours as $entry) {
            $numOrders = $entry['numOrders'] ?? 0;
            $totalOrders += $numOrders;
        }
        $averageOrdersPerHour = $totalOrders / count($numOrdersGroupedByHours) ?? 1;
        $percentage = 100 / ($averageOrdersPerHour ?? 1);
        $result = [];
        foreach ($numOrdersGroupedByHours as $entry) {
            $numOrders = $entry['numOrders'];
            $hour = $entry['hour'];
            $percentageRelativeToAverage = $numOrders * $percentage;
            if ($percentageRelativeToAverage > ConfigurationManager::getOrderHeuristicWorkingHourThresholdPercent()) {
                $result[] = $hour;
            }
        }
        return $result;
    }


    /**
     * @param null | \DateTime $startDate
     * @param null | \DateTime $endDate
     * @param $payment Payment
     * @return array
     */
    public static function getOrderDistancesByWeekday($startDate = null, $endDate = null, $payment = null)
    {
        $averagesByWeekday = OrderRepository::get()->getAvgMinutesBetweenOrdersInDateRangeByWeekday(
            $startDate ?? TimeUtils::getDateMinusDays(
                ConfigurationManager::getDaysBackToCountOrderIntervals()
            ),
            $endDate ?? TimeUtils::getNow(),
            $payment
        );
        $distances = [];
        foreach ($averagesByWeekday as $oneAverage) {
            $oneAverage['avgMinutes'] = $oneAverage['avgMinutes'] > 0
                ? $oneAverage['avgMinutes']
                : ConfigurationManager::getAverageOrderDistanceFallback();
            $distances[] = [
                'weekday' => (int) $oneAverage['weekdayNumber'],
                'tresholdMinutes' => round($oneAverage['avgMinutes'] * ConfigurationManager::getAlertMinutesSafetyBufferFactor()),
                'actualMinutes' => round($oneAverage['avgMinutes'])
            ];
        }
        return $distances;
    }


    public static function getOrderDistancesByWeekdayAndPaymentMethods()
    {
        $paymentMethods = PaymentRepository::get()->getAll();
        $distances = [];
        if (empty($paymentMethods)) {
            return $distances;
        }
        foreach ($paymentMethods as $paymentMethod) {
            $averagesByWeekday = self::getOrderDistancesByWeekday(null, null, $paymentMethod->getId());
            if (count($averagesByWeekday) < 1) {
                continue;
            }
            $distances[] = [
                'paymenttype' => $paymentMethod->getName(),
                'description' => $paymentMethod->getDescription(),
                'byWeekdays' => $averagesByWeekday
            ];
        }
        return $distances;
    }



}
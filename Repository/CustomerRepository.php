<?php
/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 09.06.20
 * Time: 14:17
 */

namespace Shopguardians\Repository;


use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;
use Mpdf\Tag\Time;
use Shopguardians\Shared\TimeUtils;

class CustomerRepository extends AbstractBaseRepository
{

    public static function get()
    {
        return new self();
    }

    public function getCustomersRegisteredTodayCount()
    {
        $today = TimeUtils::getBeginningOfToday();
        $qb = $this->getSqlQueryBuilder()
            ->select('count(users.id) as total')
            ->from('s_user', 'users')
            ->where('users.firstlogin = :today')
            ->setParameter('today', $today, Type::DATE);

        $result = $qb->execute()->fetchAll();
        return $result[0]['total'] ?? 0;
    }

    public function getCustomersTotalCount()
    {
        $qb = $this->getSqlQueryBuilder()
            ->select('count(users.id) as total')
            ->from('s_user','users');

        $result = $qb->execute()->fetchAll();
        return $result[0]['total'] ?? 0;
    }

    public function getNewsletterRegistrationsTodayCount()
    {
        $startDate = TimeUtils::getBeginningOfToday();
        $endDate = TimeUtils::getBeginningOfTomorrow();

        $qb = $this->getSqlQueryBuilder()
            ->select('count(newsletter.id) as total')
            ->from('s_campaigns_mailaddresses', 'newsletter')
            ->where('newsletter.added > :startDate')
            ->andWhere('newsletter.added < :endDate')
            ->setParameter('startDate', $startDate, Type::DATE)
            ->setParameter('endDate', $endDate, Type::DATE);

        $result = $qb->execute()->fetchAll();
        return $result[0]['total'] ?? 0;
    }

    public function getNewsletterTotalCount()
    {
        $qb = $this->getSqlQueryBuilder()
            ->select('count(newsletter.id) as total')
            ->from('s_campaigns_mailaddresses', 'newsletter');
        $result = $qb->execute()->fetchAll();
        return $result[0]['total'] ?? 0;
    }

}
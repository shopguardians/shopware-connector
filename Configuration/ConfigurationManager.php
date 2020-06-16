<?php
/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 09.06.20
 * Time: 15:21
 */

namespace Shopguardians\Configuration;

use Shopguardians\Shared\ApiTokenGenerator;

class ConfigurationManager
{

    const PLUGIN_NAME = 'Shopguardians';

    public static function getPluginName()
    {
        return self::PLUGIN_NAME;
    }

    public static function getApiKey()
    {
        return self::getUnscopedConfigParam('shoguardiansApiKey');
    }

    /**
     * get's an configuration value for a provided config param name
     *
     * @param $key
     * @return mixed
     */
    private static function getUnscopedConfigParam($key)
    {
        $reader = Shopware()->Container()->get('shopware.plugin.config_reader');
        $reader = $reader->getByPluginName(self::getPluginName());
        return $reader[$key] ?? null;
    }

    public static function getOrderHeuristicWorkingHourThresholdPercent()
    {
        return self::getUnscopedConfigParam('shoguardiansOhsWorkingHourTresholdPercent')
                    ?? 50;
    }

    public static function getDaysBackToCountOrderIntervals()
    {
        return self::getUnscopedConfigParam('shoguardiansOhsDaysBackToCountOrderIntervals')
                     ?? 60;
    }

    public static function getPaymentMethodActivitySpanDays()
    {
        return self::getUnscopedConfigParam('shoguardiansOhsPaymentMethodActivitySpan')
                    ?? 120;
    }

    public static function getAverageOrderDistanceFallback()
    {
        return self::getUnscopedConfigParam('shoguardiansOhsAverageOrderDistanceFallback')
                    ?? 120;
    }

    public static function getAlertMinutesSafetyBufferFactor()
    {
        return self::getUnscopedConfigParam('shoguardiansOhsAlertMinutesSafetyBufferFactor')
                    ?? 3;
    }

    public static function setUnscopedConfgiParam($key, $value)
    {
        $shop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->findOneBy(['default' => true]);
        $pluginManager  = Shopware()->Container()->get('shopware.plugin_Manager');
        $plugin = $pluginManager->getPluginByName(self::getPluginName());
        $pluginManager->saveConfigElement($plugin, $key, $value, $shop);
    }

    public static function generateAndSetApiKey()
    {
        $existingApiKey = self::getApiKey();
        if ($existingApiKey) {
            return;
        }
        $apiKey = ApiTokenGenerator::generateToken();
        self::setUnscopedConfgiParam('shoguardiansApiKey', $apiKey);
    }

}
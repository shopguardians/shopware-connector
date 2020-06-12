<?php
/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 10.06.20
 * Time: 16:09
 */

class Shopware_Controllers_Frontend_ShopguardiansApiSecurity
    extends \Shopguardians\ControllerShared\ShopguardiansApiBaseController
    implements \Shopware\Components\CSRFWhitelistAware
{
    public function getWhitelistedCSRFActions()
    {
        return ['versions'];
    }

    public function versionsAction()
    {
        $this->setNoRender();
        $versions = [
            'shop' => [
                'shopSystem' => 'shopware',
                'version' => Shopware()->Container()->get('config')->get('version'),
                'edition' => null
            ],
            'server' => [
                'php' => PHP_VERSION_ID,
                'software' => isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : null,
                'signature' => isset($_SERVER['SERVER_SIGNATURE']) ? $_SERVER['SERVER_SIGNATURE'] : null,
                'database' => [
                    'type' => null
                ]
            ],
        ];

        if (function_exists('apache_get_version')) {
            $versions['server']['apache'] = apache_get_version();
        }

        try {
            $version = Shopware()->Models()->getConnection()->createQueryBuilder()
                    ->select('VERSION() as version')
                    ->execute()
                    ->fetchAll()[0]['version'] ?? null;
            $versions['server']['database']['version']  = $version;
        } catch (\Exception $e) {
            //void
        }
        $this->renderJson($versions);
    }

}
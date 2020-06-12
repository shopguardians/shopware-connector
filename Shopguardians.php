<?php
/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 04.06.20
 * Time: 16:06
 */

namespace Shopguardians;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;


class Shopguardians extends Plugin
{

    public function install(Plugin\Context\InstallContext $context)
    {

    }

    public function activate(ActivateContext $context)
    {
        parent::activate($context);
    }

}
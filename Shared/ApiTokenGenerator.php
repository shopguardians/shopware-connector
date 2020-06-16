<?php
/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 16.06.20
 * Time: 10:36
 */

namespace Shopguardians\Shared;


class ApiTokenGenerator
{
    public static function generateToken()
    {
        return uniqid() . uniqid();
    }
}
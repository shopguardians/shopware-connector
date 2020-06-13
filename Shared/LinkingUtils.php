<?php
/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 08.06.20
 * Time: 13:45
 */

namespace Shopguardians\Shared;


class LinkingUtils
{

    public static function getArticleUrlFromArrayHydratedDetail($detail)
    {
        return self::getSchemeAndHostPart() . "/detail/index/sArticle/" . $detail['articleId'] ?? null;
    }

    public static function getSchemeAndHostPart()
    {
        $scheme = $_SERVER['REQUEST_SCHEME'] ?? 'https';
        $host = $_SERVER['HTTP_HOST'];
        return $scheme . "://" . $host;
    }

    /**
     * @param $detail array
     * @return string|null
     */
    public static function getLinkToIMageFromArrayHydratedDetail($detail)
    {
        $imagePath = $detail['article']['images'][0]['media']['path'] ?? null;
        if (!$imagePath) {
            return null;
        }
        $imagePath = ltrim('/');
        $imagePath = '/' . $imagePath;
        return self::getSchemeAndHostPart() . $imagePath;
    }

}
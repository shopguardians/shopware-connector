<?php
/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 08.06.20
 * Time: 13:45
 */

namespace Shopguardians\Shared;


use Shopguardians\Media\MediaUtils;
use Shopware\Models\Article\Article;

class LinkingUtils
{

    public static function getArticleUrlFromArrayHydratedDetail($detail)
    {
        return self::getSchemeAndHostPart() . "/detail/index/sArticle/" . $detail['articleId'] ?? null;
    }

    public static function getSchemeAndHostPart()
    {
        $shop = Shopware()->Shop();
        $scheme = $shop->getSecure() ? 'https' : 'http';
        $host = $shop->getHost();
        return $scheme . "://" . $host;
    }

    /**
     * @param $detail array
     * @return string|null
     */
    public static function getLinkToIMageFromArrayHydratedDetail($detail)
    {
        $mediaId = $detail['article']['images'][0]['mediaId'] ?? null;
        $imagePath = null;
        if (!$mediaId) {
            return null;
        }
        return self::getThumbnailOrDefaultPath($mediaId);
    }

    public static function getThumbnailOrDefaultPath($mediaId)
    {
        $context = Shopware()->Container()->get('shopware_storefront.context_service')->getShopContext();
        $media = Shopware()->Container()->get('shopware_storefront.media_service')->get($mediaId, $context);
        if (!$media) {
            return null;
        }
        $mediaData = Shopware()->Container()->get('legacy_struct_converter')->convertMediaStruct($media);
        if (!$mediaData) {
            return null;
        }
        $pathToReturn = $mediaData['source'] ?? null;
        $pathToReturn = MediaUtils::getImageUrlForSizeFromThumbnails(
            $mediaData['thumbnails'] ?? [],
            "200",
            "200"
        ) ?? $pathToReturn;
        return $pathToReturn;
    }

}
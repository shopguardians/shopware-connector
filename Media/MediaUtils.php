<?php
/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 18.06.20
 * Time: 12:35
 */

namespace Shopguardians\Media;

class MediaUtils
{
    /**
     * @param $thumbnails []
     * @param $width string
     * @param $height string
     * @return mixed|null
     */
    public static function getImageUrlForSizeFromThumbnails($thumbnails, $width, $height)
    {
        $thumbnails = $thumbnails ?? [];
        $found = null;
        foreach ($thumbnails as $thumbnail) {
            [$widthToTest, $heightToTest] = [$thumbnail['maxWidth'] ?? null, $thumbnail['maxHeight'] ?? null];
            if ($width === $widthToTest
                && $height === $heightToTest
            ) {
                return $thumbnail['source'];
            }
        }
        return null;
    }
}
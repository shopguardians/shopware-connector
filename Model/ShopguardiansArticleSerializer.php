<?php
/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 08.06.20
 * Time: 13:08
 */

namespace Shopguardians\Model;


use Shopguardians\Shared\LinkingUtils;

class ShopguardiansArticleSerializer
{

    /**
     * @param $details array
     * @return array
     */
    public static function fromArrayHydratedDetailCollection($details = [])
    {
        $result = [];
        foreach ($details as $detail) {
            $result[] = self::fromArrayHydratedDetail($detail);
        }
        return $result;
    }

    /**
     * @param array $detail
     * @return string[]
     */
    public static function fromArrayHydratedDetail($detail = [])
    {

        return [
            'artnum' => $detail['number'] ?? null,
            'parent_id' => $detail['articleId'] ?? null,
            'product_uid' => $detail['id'] ?? null,
            'stock' => $detail['inStock'] ?? null,
            'thumb' => LinkingUtils::getLinkToIMageFromArrayHydratedDetail($detail),
            'title' => $detail['article']['name'] ?? null,
            'url' => LinkingUtils::getArticleUrlFromArrayHydratedDetail($detail),
        ];
    }


}
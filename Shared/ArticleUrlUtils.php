<?php
/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 08.06.20
 * Time: 13:45
 */

namespace Shopguardians\Shared;


class ArticleUrlUtils
{

    public static function getArticleUrlFromArrayHydratedDetail($detail)
    {
        return "/detail/index/sArticle/" . $detail['id'] ?? null;
    }

}
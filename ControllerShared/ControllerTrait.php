<?php
/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 04.06.20
 * Time: 16:27
 */

namespace Shopguardians\ControllerShared;


trait ControllerTrait
{

    public function renderJson($bodyToRender = [])
    {
        $origin = $_SERVER['HTTP_ORIGIN'];

        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: x-api-key,Content-Type");
        header('Content-Type:application/json;charset=utf-8');
        echo json_encode($bodyToRender);
    }

    public function setNoRender()
    {
        Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();
    }

    public function handleOptionsPreflightRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
            return;
        }
        $origin = $_SERVER['HTTP_ORIGIN'];
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: x-api-key,content-type");
        header("Content-Type: application/json");
        exit();
    }


}
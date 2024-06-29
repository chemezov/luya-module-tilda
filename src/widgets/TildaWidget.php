<?php

namespace chemezov\luya\tilda\widgets;

use chemezov\luya\tilda\services\TildaAssetService;
use luya\base\Widget;
use Yii;

class TildaWidget extends Widget
{
    public int $projectId;
    public int $pageId;

    public function run()
    {
        $assetService = Yii::$container->get(TildaAssetService::class);

        $projectAssetBundle = $assetService->getProjectAsset($this->projectId);
        $pageAssetBundle = $assetService->getPageAsset($this->pageId);

        $projectAssetBundle->registerAssetFiles($this->view);
        $projectAssetBundle->registerAssetFiles($this->view);

        return $pageAssetBundle->html;
    }
}

<?php

namespace chemezov\luya\tilda\jobs;

use chemezov\luya\tilda\services\TildaAssetService;
use Yii;
use yii\base\BaseObject;
use yii\queue\Queue;
use yii\queue\RetryableJobInterface;

class GeneratePageAssetJob extends BaseObject implements RetryableJobInterface
{
    public int $pageId;

    public function execute($queue)
    {
        $assetService = Yii::$container->get(TildaAssetService::class);

        $assetService->generatePageAsset($this->pageId);
    }

    public function getTtr()
    {
        return 60 * 5; // 5 minutes
    }

    public function canRetry($attempt, $error): bool
    {
        return $attempt < 10;
    }
}

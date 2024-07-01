<?php

namespace chemezov\luya\tilda\jobs;

use chemezov\luya\tilda\services\TildaAssetService;
use Yii;
use yii\base\BaseObject;
use yii\queue\Queue;
use yii\queue\RetryableJobInterface;

class GenerateProjectAssetJob extends BaseObject implements RetryableJobInterface
{
    public int $projectId;

    public function execute($queue)
    {
        $assetService = Yii::$container->get(TildaAssetService::class);

        $assetService->generateProjectAsset($this->projectId);
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

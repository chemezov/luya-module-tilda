<?php

namespace chemezov\luya\tilda\frontend\controllers;

use chemezov\luya\tilda\frontend\Module;
use chemezov\luya\tilda\helpers\TildaCacheHelper;
use chemezov\luya\tilda\jobs\GeneratePageAssetJob;
use chemezov\luya\tilda\jobs\GenerateProjectAssetJob;
use chemezov\luya\tilda\services\TildaAssetService;
use chemezov\luya\tilda\services\TildaService;
use luya\cms\frontend\base\Controller;
use yii\caching\Cache;
use yii\queue\Queue;
use yii\web\UnprocessableEntityHttpException;

/**
 * @property Module $module
 */
class TildaController extends Controller
{
    protected TildaService $service;
    protected TildaAssetService $assetService;
    protected Cache $cache;
    protected Queue $queue;

    public function __construct($id, $module, TildaService $service, TildaAssetService $assetService, $config = [])
    {
        $this->service = $service;
        $this->assetService = $assetService;

        parent::__construct($id, $module, $config);
    }

    public function init()
    {
        parent::init();

        $this->cache = $this->module->cache;
        $this->queue = $this->module->queue;
    }

    public function actionWebhook($pageid = '', $projectid = '', $published = '', $publickey = '')
    {
        if (!$this->verifyPublicKey($publickey)) {
            throw new UnprocessableEntityHttpException('Invalid public key');
        }

        if ($projectid) {
            // Delete page list cache key
            $cacheKey = TildaCacheHelper::getPageListCacheKey($projectid);
            $this->cache->delete($cacheKey);

            // Delete project export cache key
            $cacheKey = TildaCacheHelper::getProjectExportCacheKey($projectid);
            $this->cache->delete($cacheKey);

            $this->queue->push(new GenerateProjectAssetJob([
                'projectId' => $projectid,
            ]));
        }

        if ($pageid) {
            // Delete project export cache key
            $cacheKey = TildaCacheHelper::getPageExportCacheKey($pageid);
            $this->cache->delete($cacheKey);

            $this->queue->push(new GeneratePageAssetJob([
                'pageId' => $pageid,
            ]));
        }

        $cacheKey = TildaCacheHelper::getProjectListCacheKey();
        $this->cache->delete($cacheKey);

        echo 'ok';
    }

    protected function verifyPublicKey($publicKey): bool
    {
        return $this->module->publicKey === $publicKey;
    }
}

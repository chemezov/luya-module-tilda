<?php

namespace chemezov\luya\tilda\services;

use chemezov\luya\tilda\helpers\TildaCacheHelper;
use luya\helpers\ArrayHelper;
use TildaTools\Tilda\Objects\Page\ExportedPage;
use TildaTools\Tilda\Objects\Project\ExportedProject;
use TildaTools\Tilda\TildaApi;
use yii\base\BaseObject;
use yii\caching\Cache;
use yii\caching\TagDependency;

/**
 * @property-read TildaApi $api
 */
class TildaService extends BaseObject
{
    public int $pageListCacheDuration = 60;

    protected TildaApi $api;
    protected Cache $cache;

    public function __construct(TildaApi $api, Cache $cache, $config = [])
    {
        $this->api = $api;
        $this->cache = $cache;

        parent::__construct($config);
    }

    public function getProjectList(): array
    {
        $cacheKey = TildaCacheHelper::getProjectListCacheKey();

        return $this->cache->getOrSet($cacheKey, function () {
            $data = $this->api->getProjectsList();

            return ArrayHelper::map($data, 'id', 'title');
        });
    }

    public function getPageList(int $projectId): array
    {
        $cacheKey = TildaCacheHelper::getPageListCacheKey($projectId);

        return $this->cache->getOrSet($cacheKey, function () use ($projectId) {
            $data = $this->api->getPagesList($projectId);

            return ArrayHelper::map($data, 'id', 'title');
        }, $this->pageListCacheDuration);
    }

    public function getPageExport(int $pageId): ExportedPage
    {
        $cacheKey = TildaCacheHelper::getPageExportCacheKey($pageId);

        return $this->cache->getOrSet($cacheKey, function () use ($pageId) {
            return $this->api->getPageExport($pageId);
        });
    }

    public function getProjectExport(int $projectId): ExportedProject
    {
        $cacheKey = TildaCacheHelper::getProjectExportCacheKey($projectId);

        return $this->cache->getOrSet($cacheKey, function () use ($projectId) {
            return $this->api->getProjectExport($projectId);
        });
    }
}

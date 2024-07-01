<?php

namespace chemezov\luya\tilda\services;

use chemezov\luya\tilda\assets\TildaAsset;
use chemezov\luya\tilda\base\DownloadAssetException;
use chemezov\luya\tilda\helpers\TildaCacheHelper;
use luya\helpers\FileHelper;
use TildaTools\Tilda\Objects\Page\ExportedPage;
use TildaTools\Tilda\Objects\Project\ExportedProject;
use Yii;
use yii\base\BaseObject;
use yii\caching\Cache;

class TildaAssetService extends BaseObject
{
    public const CSS_PATH = 'css';
    public const JS_PATH = 'js';
    public const IMG_PATH = 'img';

    public string $basePath = '@webroot/uploads/tilda';
    public string $baseUrl = '/uploads/tilda';

    protected TildaService $service;
    protected Cache $cache;

    public function __construct(TildaService $service, Cache $cache, $config = [])
    {
        $this->service = $service;
        $this->cache = $cache;

        parent::__construct($config);
    }

    public function getProjectAsset(int $projectId): TildaAsset
    {
        $cacheKey = TildaCacheHelper::getProjectAssetCacheKey($projectId);

        return $this->cache->getOrSet($cacheKey, function () use ($projectId) {
            return $this->generateProjectAsset($projectId);
        });
    }

    public function getPageAsset(int $pageId): TildaAsset
    {
        $cacheKey = TildaCacheHelper::getPageAssetCacheKey($pageId);

        return $this->cache->getOrSet($cacheKey, function () use ($pageId) {
            return $this->generatePageAsset($pageId);
        });
    }

    public function generateProjectAsset(int $projectId): TildaAsset
    {
        $cacheKey = TildaCacheHelper::getProjectAssetCacheKey($projectId);

        $assetPath = $this->getProjectAssetPath($projectId);
        $assetUrl = $this->getProjectAssetUrl($projectId);
        $project = $this->service->getProjectExport($projectId);

        $bundle = $this->generateAsset($assetPath, $assetUrl, $project);

        $this->cache->set($cacheKey, $bundle);

        return $bundle;
    }

    public function generatePageAsset(int $pageId): TildaAsset
    {
        $cacheKey = TildaCacheHelper::getPageAssetCacheKey($pageId);

        $page = $this->service->getPageExport($pageId);
        $projectId = $page->projectid;
        $assetPath = $this->getProjectAssetPath($projectId);
        $assetUrl = $this->getProjectAssetUrl($projectId);

        $bundle = $this->generateAsset($assetPath, $assetUrl, $page);
        $bundle->html = $page->html;

        $this->cache->set($cacheKey, $bundle);

        return $bundle;
    }

    /**
     * @param string $path
     * @param string $baseUrl
     * @param ExportedPage|ExportedProject $object
     * @return TildaAsset
     * @throws DownloadAssetException
     * @throws \yii\base\Exception
     */
    protected function generateAsset(string $path, string $baseUrl, $object): TildaAsset
    {
        // Build bundle
        $bundle = new TildaAsset();
        $bundle->basePath = $path;
        $bundle->baseUrl = $baseUrl;
        $bundle->publishOptions = [
            'only' => [
                self::CSS_PATH . '/*',
                self::JS_PATH . '/*',
            ],
        ];

        // Pre-flight init
        $cssPath = $path . DIRECTORY_SEPARATOR . self::CSS_PATH;
        $jsPath = $path . DIRECTORY_SEPARATOR . self::JS_PATH;
        $imgPath = $path . DIRECTORY_SEPARATOR . self::IMG_PATH;

        FileHelper::createDirectory($cssPath);
        FileHelper::createDirectory($jsPath);
        FileHelper::createDirectory($imgPath);

        // Download files
        if (is_array($object->js) && count($object->js) > 0) {
            foreach ($object->js as $asset) {
                $source = $asset->from;
                $target = $jsPath . DIRECTORY_SEPARATOR . $asset->to;

                if ($this->download($source, $target)) {
                    $bundle->js[] = self::JS_PATH . '/' . $asset->to;
                    usleep(200);
                }
            }
        }

        if (is_array($object->css) && count($object->css) > 0) {
            foreach ($object->css as $asset) {
                $source = $asset->from;
                $target = $cssPath . DIRECTORY_SEPARATOR . $asset->to;

                if ($this->download($source, $target)) {
                    $bundle->css[] = self::CSS_PATH . '/' . $asset->to;
                    usleep(200);
                }
            }
        }

        if (is_array($object->images) && count($object->images) > 0) {
            foreach ($object->images as $asset) {
                $source = $asset->from;
                $target = $imgPath . DIRECTORY_SEPARATOR . $asset->to;

                $this->download($source, $target);
                usleep(200);
            }
        }

        return $bundle;
    }

    public function getBasePath(): string
    {
        return Yii::getAlias(rtrim($this->basePath, '\\/'));
    }

    public function getProjectAssetPath(int $projectId): string
    {
        return $this->getBasePath() . '/project' . $projectId;
    }

    public function getBaseUrl(): string
    {
        return Yii::getAlias(rtrim($this->baseUrl, '\\/'));
    }

    public function getProjectAssetUrl(int $projectId): string
    {
        return $this->getBaseUrl() . '/project' . $projectId;
    }

    protected function download(string $source, string $target): bool
    {
        $data = file_get_contents($source);

        if ($data !== false) {
            file_put_contents($target, $data);

            return true;
        } else {
            throw new DownloadAssetException("Ошибка при загрузке файла с URL: {$source}");
        }
    }
}

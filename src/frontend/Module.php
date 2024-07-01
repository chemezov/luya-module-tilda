<?php

namespace chemezov\luya\tilda\frontend;

use chemezov\luya\tilda\services\TildaAssetService;
use chemezov\luya\tilda\services\TildaService;
use luya\cms\frontend\blockgroups\ProjectGroup;
use TildaTools\Tilda\TildaApi;
use Yii;
use yii\base\Application;
use yii\base\InvalidConfigException;
use yii\caching\Cache;
use yii\di\Instance;
use yii\queue\Queue;

/**
 * Tilda Frontend Module.
 *
 * File has been created with `module/create` command.
 *
 * @author
 * @since 1.0.0
 */
class Module extends \luya\base\Module
{
    public array $urlRules = [
        'tilda/webhook' => 'tilda/tilda/webhook',
    ];

    /**
     * @var Cache|array|string the Cache object or the application component ID.
     */
    public $cache = 'cache';

    /**
     * @var Queue|array|string the Queue object or the application component ID.
     */
    public $queue = 'adminqueue';

    /**
     * @var string Tilda public key.
     */
    public string $publicKey;

    /**
     * @var string Tilda secret key.
     */
    public string $secretKey;

    /**
     * @var array Project IDs. If array is empty - will show all projects.
     */
    public array $projectIds = [];

    public string $blockGroup = ProjectGroup::class;

    /**
     * @var array|string
     */
    public $defaultBlockCssClasses = [];

    /**
     * Tilda files will be loaded to this path. Path must be accessible via web request.
     *
     * @var string
     */
    public string $assetsPath = '@webroot/uploads/tilda';

    /**
     * Base url for Tilda files. Must be mapped to {@see $assetsPath}.
     *
     * @var string
     */
    public string $assetsUrl = '/uploads/tilda';

    /**
     * @param Application|\luya\web\Application|\luya\console\Application $app
     * @return void
     * @throws InvalidConfigException
     */
    public function luyaBootstrap(Application $app)
    {
        parent::luyaBootstrap($app);

        $container = Yii::$container;

        // Prepare properties
        $this->cache = Instance::ensure($this->cache, Cache::class);
        $this->queue = Instance::ensure($this->queue, Queue::class);

        // Check configuration
        if (!isset($this->publicKey) || !isset($this->secretKey)) {
            throw new InvalidConfigException('Tilda public key and secret must be set.');
        }

        // Set DI components
        $container->setSingleton(TildaApi::class, fn() => new TildaApi([
            TildaApi::CONFIG_OPTION_PUBLIC_KEY => $this->publicKey,
            TildaApi::CONFIG_OPTION_SECRET_KEY => $this->secretKey,
        ]));

        $container->setSingleton(TildaService::class, fn() => new TildaService($container->get(TildaApi::class), $this->cache));
        $container->setSingleton(TildaAssetService::class, fn() => new TildaAssetService($container->get(TildaService::class), $this->cache, [
            'basePath' => $this->assetsPath,
            'baseUrl' => $this->assetsUrl,
        ]));
    }
}

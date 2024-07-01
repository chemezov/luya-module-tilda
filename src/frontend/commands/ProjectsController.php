<?php

namespace chemezov\luya\tilda\frontend\commands;

use chemezov\luya\tilda\frontend\Module;
use luya\console\Command;
use luya\helpers\ArrayHelper;
use TildaTools\Tilda\TildaApi;
use Yii;
use yii\console\ExitCode;
use yii\console\widgets\Table;

/**
 * @property Module $module
 */
class ProjectsController extends Command
{
    protected TildaApi $api;

    public function __construct($id, $module, TildaApi $api, $config = [])
    {
        $this->api = $api;

        parent::__construct($id, $module, $config);
    }

    /**
     * List Tilda Projects.
     *
     * @return int Exit code
     * @throws \TildaTools\Tilda\Exceptions\Api\TildaApiException
     * @throws \TildaTools\Tilda\Exceptions\InvalidJsonException
     * @throws \TildaTools\Tilda\Exceptions\Map\MapperNotFoundException
     */
    public function actionIndex(): int
    {
        // Get and normalize project list
        $projects = $this->api->getProjectsList();
        $list = ArrayHelper::map($projects, 'id', 'title');

        // Build table with projects (ID => title)
        $table = new Table();
        $table->setHeaders(['Project ID', 'Title']);

        $rows = [];

        foreach ($list as $key => $value) {
            $rows[] = [$key, $value];
        }

        $table->setRows($rows);
        echo $table->run();

        return ExitCode::OK;
    }
}

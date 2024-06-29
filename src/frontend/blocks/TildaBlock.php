<?php

namespace chemezov\luya\tilda\frontend\blocks;

use chemezov\luya\tilda\frontend\Module;
use chemezov\luya\tilda\services\TildaService;
use luya\cms\base\PhpBlock;
use luya\cms\helpers\BlockHelper;
use luya\helpers\ArrayHelper;

/**
 * Tilda Block.
 *
 * @property Module $module
 */
class TildaBlock extends PhpBlock
{
    protected TildaService $service;

    public function __construct(TildaService $service, $config = [])
    {
        $this->service = $service;

        parent::__construct($config);
    }

    /**
     * @inheritDoc
     */
    public function blockGroup()
    {
        return Module::getInstance()->blockGroup;
    }

    /**
     * @inheritDoc
     */
    public function name()
    {
        return 'Tilda Block';
    }

    /**
     * @inheritDoc
     */
    public function icon()
    {
        return 'title'; // see the list of icons on: https://material.io/icons/
    }

    /**
     * @inheritDoc
     */
    public function config()
    {
        return [
            'vars' => [
                ['var' => 'code', 'label' => 'Page', 'type' => self::TYPE_SELECT, 'options' => BlockHelper::selectArrayOption($this->getPageList())],
            ],
            'cfgs' => [
                ['var' => 'cssClasses', 'label' => 'CSS Classes', 'type' => self::TYPE_TEXT],
            ],
        ];
    }

    public function extraVars()
    {
        $code = $this->getVarValue('code');

        $cssClasses = $this->getCfgValue('cssClasses');
        $defaultCssClasses = (array)Module::getInstance()->defaultBlockCssClasses;

        $cssClasses = explode(' ', trim($cssClasses));
        $cssClasses = array_merge($defaultCssClasses, $cssClasses);
        $cssClasses = array_filter($cssClasses);

        return [
            'pageTitle' => $this->getPageList()[$code] ?? null,
            'projectId' => $this->getProjectId(),
            'pageId' => $this->getPageId(),
            'cssClasses' => $cssClasses,
        ];
    }

    public function getPageList(): array
    {
        $projects = $this->service->getProjectList();
        $projectIds = Module::getInstance()->projectIds;

        // If project list is empty, output all projects
        if (!empty($projectIds)) {
            $projects = array_intersect_key($projects, array_flip($projectIds));
        }

        $list = [];

        foreach ($projects as $projectId => $projectTitle) {
            $pages = $this->service->getPageList($projectId);
            $data = [];

            foreach ($pages as $id => $title) {
                // If projects count more than 1, add prefix with project title
                if (count($projects) > 1) {
                    $title = '[' . $projectTitle . '] ' . $title;
                }

                $data[$projectId . '_' . $id] = $title;
            }

            $list = ArrayHelper::merge($list, $data);
        }

        return $list;
    }

    public function getProjectId(): ?int
    {
        $code = $this->getVarValue('code');

        if ($code) {
            return explode('_', $code)[0] ?? null;
        }

        return null;
    }

    public function getPageId(): ?int
    {
        $code = $this->getVarValue('code');

        if ($code) {
            return explode('_', $code)[1] ?? null;
        }

        return null;
    }

    /**
     * {@inheritDoc}
     *
     * @param {{cfgs.projectId}}
     * @param {{vars.blockId}}
     */
    public function admin()
    {
        return '<h5 class="mb-3">Tilda Block</h5>' .
            '<table class="table table-bordered">' .
            '{% if vars.code is not empty %}' .
            '<tr><td><b>Page</b></td><td>{{extras.pageTitle}}</td></tr>' .
            '{% endif %}' .
            '</table>';
    }
}

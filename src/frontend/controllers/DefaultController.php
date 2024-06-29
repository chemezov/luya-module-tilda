<?php

namespace chemezov\luya\tilda\frontend\controllers;

use chemezov\luya\tilda\services\TildaService;
use luya\cms\frontend\base\Controller;
use TildaTools\Tilda\TildaApi;

class DefaultController extends Controller
{
    protected $service;

    public function __construct($id, $module, TildaService $service, $config = [])
    {
        $this->service = $service;

        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
    }
}

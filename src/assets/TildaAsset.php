<?php

namespace chemezov\luya\tilda\assets;

use yii\web\AssetBundle;
use yii\web\View;

class TildaAsset extends AssetBundle
{
    public $jsOptions = ['position' => View::POS_HEAD];

    public $html;
}

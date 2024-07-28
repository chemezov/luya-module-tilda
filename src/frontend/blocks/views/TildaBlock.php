<?php
/**
 * View file for block: TildaBlock
 *
 * File has been created with `block/create` command.
 *
 * $this->cfgValue('projectId');
 * $this->varValue('blockId');
 *
 * @var \luya\cms\base\PhpBlockView $this
 */

use chemezov\luya\tilda\widgets\TildaWidget;
use yii\helpers\Html;

$projectId = $this->extraValue('projectId');
$pageId = $this->extraValue('pageId');
$cssClasses = $this->extraValue('cssClasses');

$outputContainer = !empty($cssClasses);

if (!empty($projectId) && !empty($pageId)) {
    if ($outputContainer) {
        echo Html::beginTag('div', ['class' => $cssClasses]);
    }

    echo Html::beginTag('div', ['class' => 'tilda-inner-container']);
    echo TildaWidget::widget([
        'projectId' => $projectId,
        'pageId' => $pageId,
    ]);
    echo Html::endTag('div');

    if ($outputContainer) {
        echo Html::endTag('div');
    }
}

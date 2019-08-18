<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\components\Helper;

$this->title = 'History of ' . Html::encode($city['name']) . ' (' . Html::encode($city['country']['name']) . ')';
$this->params['breadcrumbs'][] = $this->title;
$symbol = Html::decode('&#8451;');
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <?= $this->title ?>
    </div>
    <div class="panel-body">
        <div class="row">
            <?php foreach ($forecasts as $day => $forecast) { ?>
            <div class="col-xs-3">
                <p>
                    <strong><?= Html::encode($day) ?></strong>
                </p>
                <?php foreach ($forecast as $item) {?>
                <p>
                    <?= Yii::$app->formatter->asDate($item['when_created'], 'php:H:i:s') ?> <?= Helper::convertFahrenheitToCelsius($item['temperature']) ?> <?= $symbol ?>
                </p>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
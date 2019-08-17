<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Stats';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="panel panel-default">
    <div class="panel-body">
        <div class="base-container" id="grid-StatsForm">
            <div class="table-responsive">
                <?= \nullref\datatable\DataTable::widget([
                    'tableOptions' => [
                        'class' => 'table',
                    ],
                    'columns' => [
                        'Country',
                        'City',
                        'Max temperature',
                        'Min temperature',
                        'Avg temperature',
                        'Actions',
                    ],
                    'serverSide' => true,
                    'ajax' => [
                        'url' => '/index.php/site/datatables-stats',
                        'type'=> 'POST'
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>

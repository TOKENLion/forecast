<?php

namespace console\controllers;
use backend\jobs\ForecastJob;
use common\models\City;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class JobController extends Controller {
    public function actionGetForecast($city, $start, $end)
    {
        $currentCity = City::find()->where(['name' => $city])->one();
        if (empty($currentCity)) {
            exit('Sorry, but select city doesn\'t fount!');
        }

        $currentCity->dateStart = $start;
        $currentCity->dateEnd = $end;
        $currentCity->scenario = 'job';
        if (!$currentCity->validate()) {
            $errors = $currentCity->getErrorSummary(1);
            $errors = implode("\n", $errors);
            exit($errors);
        }

        Yii::$app->queue->push(new ForecastJob([
            'city' => $city,
            'start' => $start,
            'end' => $end,
        ]));
    }
}
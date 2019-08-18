<?php
namespace frontend\controllers;

use Yii;
use yii\helpers\Html;
use yii\web\Controller;
use common\models\City;
use common\models\Forecast;
use common\components\Helper;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionStatistics()
    {
        $data = [
            'dateStart' => Yii::$app->formatter->asDate('-1 day', 'php:d.m.Y'),
            'dateEnd' => Yii::$app->formatter->asDate('now', 'php:d.m.Y')
        ];

        return $this->render('statistics', $data);
    }

    public function actionHistory($city = "")
    {
        $currentCity = array();
        if (!empty($city)) {
            $currentCity = City::getCityByName($city);
        } else {
            $data['error'] = 'Sorry, but city doesn\'t set on URL!';
        }

        if (empty($data['error']) && empty($currentCity)) {
            $data['error'] = 'Sorry, but history for select city doesn\'t found!';
        }

        $data['city'] = $currentCity;

        $forecasts = Forecast::getForecastByCityId($currentCity['id']);

        foreach ($forecasts as $forecast) {
            $date = Yii::$app->formatter->asDate($forecast['when_created'], 'php:M d, Y');
            $data['forecasts'][$date][] = $forecast;
        }

        return $this->render('history', $data);
    }

    public function actionDatatablesStatistics()
    {
        $request = Yii::$app->request;

        $forecasts = array();
        $dateStart = Yii::$app->formatter->asDate($request->post('date_start'), 'php:U');
        $dateEnd = Yii::$app->formatter->asDate($request->post('date_end'), 'php:U');
        $totalForecasts = Forecast::getCountForecastByInterval($dateStart, $dateEnd);

        if (!empty($totalForecasts)) {
            $forecasts = Forecast::getForecastByInterval($dateStart, $dateEnd, $request->post('length'), $request->post('start'));
        }

        $output = array(
            "draw" => $request->post('draw'),
            "recordsTotal" => $totalForecasts,
            "recordsFiltered" => count($forecasts),
            "data" => []
        );

        $symbol = '&#8451;';
        if (!empty($forecasts)) {
            foreach ($forecasts as $forecast) {
                $output['data'][] = [
                    'country' => $forecast['city']['country']['name'],
                    'city' => $forecast['city']['name'],
                    'max_temperature' => Helper::convertFahrenheitToCelsius($forecast['max']) . Html::decode($symbol),
                    'min_temperature' => Helper::convertFahrenheitToCelsius($forecast['min']) . Html::decode($symbol),
                    'avg_temperature' => Helper::convertFahrenheitToCelsius($forecast['avg']) . Html::decode($symbol),
                    'actions' => '',
                ];
            }
        }

        return $this->asJson($output);
    }
}

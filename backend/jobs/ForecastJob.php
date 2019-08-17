<?php

namespace backend\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use common\models\City;
use common\models\Forecast;
use SimpleXMLElement;

class ForecastJob extends BaseObject implements JobInterface
{
    public $city;
    public $start;
    public $end;


    /**
     * Check if string is XML format
     *
     * @param $response
     */
    private function isXml($response)
    {
        libxml_use_internal_errors(true);

        $doc = simplexml_load_string($response);
        $xml = explode("\n", $response);

        if (!$doc) {
            $errors = libxml_get_errors();

            foreach ($errors as $error) {
                exit("API: " . display_xml_error($error, $xml) . "\n");
            }

            libxml_clear_errors();
        }
    }

    public function execute($queue)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => Yii::$app->params['siteForecast'] . "?start={$this->start}&end={$this->end}&city={$this->city}",
            CURLOPT_RETURNTRANSFER => true,
        ));

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            exit("API: {$error}\n");
        }

        if (empty($response)) {
            $this->isXml($response);
        }

        $xml = new SimpleXMLElement($response);
        $json= json_encode($xml);
        $xml = json_decode($json,TRUE);

        if (!empty($xml['error'])) {
            exit("API: {$this->city} {$xml['error']}\n");
        }

        $city = City::find()->where(['name' => $this->city])->one();
        $forecasts = array();
        foreach ($xml['row'] as $row) {
            $hash = md5($row['city'] . $row['ts']);
            if (!empty($forecasts[$hash])) {
                continue;
            }

            $forecasts[$hash] = [
                $city->id,
                $row['temperature'],
                $row['ts']
            ];

            $conditionWhenCreated[] = $row['ts'];
        }

        $forecastsDb = Forecast::find()
            ->where([
                'when_created' => $conditionWhenCreated,
                'city_id' => $city->id
            ])
            ->asArray()
            ->all();

        $forecastsChange = array();
        if (!empty($forecastsDb)) {
            foreach ($forecastsDb as $forecast) {
                $hashRecord = md5($this->city . $forecast['when_created']);
                $forecastsChange[$hashRecord] = $forecast;
            }
            $forecasts = array_diff_assoc($forecasts, $forecastsChange);
        }

        $countRecords = 0;
        if (!empty($forecasts)) {
            $countRecords = Yii::$app->db
                ->createCommand()
                ->batchInsert('forecast', ['city_id', 'temperature', 'when_created'], $forecasts)
                ->execute();
        }

        exit("[" . $this->city . " | " . date('Y-m-d H:i:s') . "]Forecasts inserted: {$countRecords}\n");
    }

//    public function execute($queue)
//    {
//        echo '<pre>' . print_r(Yii::$app->params['siteForecast'], true) . '</pre>';
//        exit();
//        $mh = curl_multi_init();
//        $requestCurl = $multiRequestCurl[] = curl_init();
//        $index = null;
//        curl_setopt($requestCurl, CURLOPT_URL, Yii::$app->params['siteForecast'] . "?start={$this->start}&end={$this->end}&city={$this->city}");
//        curl_setopt($requestCurl, CURLOPT_HEADER, 0);
//        curl_setopt($requestCurl, CURLOPT_RETURNTRANSFER, 1);
//        curl_multi_add_handle($mh, $requestCurl);
//
//        do {
//            curl_multi_exec($mh, $index);
//        } while ($index > 0);
//
//        foreach ($multiRequestCurl as $k => $ch) {
//            $result[] = curl_multi_getcontent($ch);
//            curl_multi_remove_handle($mh, $ch);
//        }
//        var_dump($result);
//        //close
//        curl_multi_close($mh);
//
//        echo '<pre>' . print_r($this->city, true) . '</pre>';
//        echo '<pre>' . print_r($this->start, true) . '</pre>';
//        echo '<pre>' . print_r($this->end, true) . '</pre>';
//        exit();
//    }
}
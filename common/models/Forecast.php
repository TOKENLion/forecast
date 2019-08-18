<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "forecast".
 *
 * @property int $id
 * @property int $city_id
 * @property double $temperature
 * @property string $when_created
 *
 * @property City $city
 */
class Forecast extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'forecast';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['city_id', 'temperature'], 'required'],
            [['city_id'], 'default', 'value' => null],
            [['city_id'], 'integer'],
            [['temperature'], 'number'],
            [['when_created'], 'string'],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'city_id' => 'City ID',
            'temperature' => 'Temperature',
            'when_created' => 'When Created',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    public static function getForecastByCityId($cityId) {
        return self::find()
            ->where(['city_id' => $cityId])
            ->asArray()
            ->all();
    }

    public static function getCountForecastByInterval($dateStart, $dateEnd) {
        return self::find()
            ->select('city_id, COUNT(DISTINCT city_id) as cities')
            ->where([
                'between',
                'when_created',
                $dateStart,
                $dateEnd,
            ])
            ->groupBy('city_id')
            ->sum('c.cities');
    }

    public static function getForecastByInterval($dateStart, $dateEnd, $start, $limit) {
        return self::find()
            ->with(['city', 'city.country'])
            ->select('city_id, min(temperature), max(temperature), avg(temperature)')
            ->where([
                'between',
                'when_created',
                $dateStart,
                $dateEnd
            ])
            ->groupBy('city_id')
//                ->orderBy($orderBy)
            ->limit($start)
            ->offset($limit)
            ->asArray()
            ->all();
    }
}

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cities".
 *
 * @property int $id
 * @property int $country_id
 * @property string $name
 *
 * @property Country $country
 */
class City extends \yii\db\ActiveRecord
{
    public $dateStart;
    public $dateEnd;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cities';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country_id', 'name'], 'required'],
            [['country_id'], 'default', 'value' => null],
            [['country_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
//            [['name'], 'unique'],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['dateStart','dateEnd'], 'date', 'on' => 'job', 'format' => 'php:d.m.Y'],
            ['dateEnd', 'compare', 'compareAttribute' => 'dateStart', 'operator' => '>'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'country_id' => 'Country ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }
}

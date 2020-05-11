<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "rates".
 *
 * @property int $id
 * @property string $symbol
 * @property string $name
 * @property float $rate_to_RON
 * @property float $rate_to_EUR
 * @property float $rate_to_USD
 * @property float $rate_to_GBP
 * @property float $rate_to_CHF
 * @property float $rate_to_XAU
 * @property string $date
 */
class Currency extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rates';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['symbol', 'name', 'rate_to_RON', 'rate_to_EUR', 'rate_to_USD', 'rate_to_GBP', 'rate_to_CHF', 'rate_to_XAU', 'date'], 'required'],
            [['rate_to_RON', 'rate_to_EUR', 'rate_to_USD', 'rate_to_GBP', 'rate_to_CHF', 'rate_to_XAU'], 'number'],
            [['date'], 'safe'],
            [['symbol'], 'string', 'max' => 3],
            [['name'], 'string', 'max' => 40],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => Yii::t('app', 'ID'),
            'symbol'      => Yii::t('app', 'Symbol'),
            'name'        => Yii::t('app', 'Name'),
            'rate_to_RON' => Yii::t('app', 'Rate To RON'),
            'rate_to_EUR' => Yii::t('app', 'Rate To EUR'),
            'rate_to_USD' => Yii::t('app', 'Rate To USD'),
            'rate_to_GBP' => Yii::t('app', 'Rate To GBP'),
            'rate_to_CHF' => Yii::t('app', 'Rate To CHF'),
            'rate_to_XAU' => Yii::t('app', 'Rate To XAU'),
            'date'        => Yii::t('app', 'Date'),
        ];
    }

    /**
     * Finds Currency by Symbol.
     *
     * @param  string $symbol
     * @return static|null
     */
    public static function findBySymbol($symbol)
    {
        return static::findOne(['symbol' => $symbol]);
    }

    /**
     * Finds Currency by Date.
     *
     * @param  string $date
     * @return static|null
     */
    public static function findByDate($date)
    {
        return static::findOne(['date' => $date]);
    }

}
<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "exchange".
 *
 * @property int $id ID
 * @property string $code Currency ISO code
 * @property int $multiplier Currency multiplier
 * @property float $rate Rate
 * @property string $date Date
 */
class CurrencyRate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exchange';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'rate'], 'required'],
            [['multiplier'], 'integer'],
            [['rate'], 'number'],
            [['date'], 'safe'],
            [['code'], 'string', 'max' => 3],
            [['code', 'date'], 'unique', 'targetAttribute' => ['code', 'date']]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('app', 'ID'),
            'code'       => Yii::t('app', 'Code'),
            'multiplier' => Yii::t('app', 'Multiplier'),
            'rate'       => Yii::t('app', 'Rate'),
            'date'       => Yii::t('app', 'Date')
        ];
    }
}
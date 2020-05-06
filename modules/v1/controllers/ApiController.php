<?php

namespace app\modules\v1\controllers;

use app\helpers\GetRates;
use app\models\CurrencyRate;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\Response;

class ApiController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class'   => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'verbs'             => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'index'  => ['GET', 'POST'],
                    'view'   => ['GET'],
                    'create' => ['POST'],
                    'update' => ['PUT'],
                    'delete' => ['DELETE'],
                ],
            ],
        ];
    }

    /**
     * Lists all CurrencyRate models.
     * @return mixed
     */
    public function actionIndex()
    {
        $data = CurrencyRate::find()->all();

        return $data;
    }

    /**
     * Displays a filtered list by date or currency code of CurrencyRate models.
     * @param integer $id
     * @return mixed
     */
    public function actionFilter($date = null, $code = null)
    {
        if (!$date) {
            (date('H') <= 13) ?
            $date = date("Y-m-d", strtotime('-1 days')) :
            $date = date("Y-m-d");
        }
        if (date('N', strtotime($date)) >= 5) {
            $date = date("Y-m-d", strtotime('previous friday', strtotime($date)));
        }

        $data         = CurrencyRate::find()->where(['date' => $date])->andFilterWhere(['code' => $code])->all();
        $currencyList = [];

        foreach ($data as $currency) {
            $code                = $currency[code];
            $rate                = $currency[rate];
            $currencyList[$code] = $rate;
        }

        if ($data === []) {
            return ['message' => 'No Currency data available for chosen day'];
        } else {
            if (count($currencyList) > 1) {
                return ['date' => $date, 'rate' => $currencyList];
            } else {
                return ['date' => $date, $code => $currencyList[$code]];
            }
        };
    }

    /**
     * Delets the exchange rates found in the db for the specified year
     * Fetches new yearly exchange rates from BNR for the specified year
     * and writes them in the db
     * @param integer $year
     * @return mixed
     */
    public function actionYearly($year)
    {
        return GetRates::getYearly($year);
    }

    /**
     * Fetches last 10 days of exchange rates from BNR checks for any
     * that already exist in the db and writes the ones missing in the db
     * @return mixed
     */
    public function actionTendays()
    {
        return GetRates::getLastTenDays();
    }

    /**
     * Fetches daily exchange rates from BNR and writes them in the db
     * @return mixed
     */
    public function actionDaily()
    {
        return GetRates::getDaily();
    }
}

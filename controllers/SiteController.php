<?php

namespace app\controllers;

use app\helpers\GetRates;
use app\models\CurrencyRate;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * CurrencyRateController implements the CRUD actions for CurrencyRate model.
 */
class SiteController extends Controller
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
     * @throws NotFoundHttpException if the model cannot be found
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

        $data = CurrencyRate::find()->where(['date' => $date])->andFilterWhere(['code' => $code])->all();

        return $data;
    }

    /**
     * Finds the CurrencyRate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CurrencyRate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function _findModel($id)
    {
        if (($model = CurrencyRate::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    //------------------------------------------------------------------------------------------------//
    // HELPERS
    //------------------------------------------------------------------------------------------------//

    /**
     * Delets the exchange rates found in the db
     * for the specified year
     * Fetches new yearly exchange rates from BNR for
     * the specified year
     * and writes them in the db
     */
    public function actionYearly($year)
    {
        GetRates::getYearly($year);
    }

    /**
     * Fetches last 10 days of exchange rates from BNR
     * checks for any that already exist in the db
     * and writes the ones missing in the db
     */
    public function actionTendays()
    {
        GetRates::getLastTenDays();
    }

    /**
     * Fetches daily exchange rates from
     * BNR and writes them in the db
     */
    public function actionDaily()
    {
        GetRates::getDaily();
    }
}

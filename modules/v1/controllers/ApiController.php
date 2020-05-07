<?php

namespace app\modules\v1\controllers;

use app\helpers\GetRates;
use app\models\CurrencyRate;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\Response;

class ApiController extends Controller
{
    public function init()
    {
        parent::init();
    }

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
                    'filter' => ['GET', 'POST'],
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
        return ['message' => 'nothing here'];
    }

    /**
     * Displays a filtered list by date or currency code of CurrencyRate models.
     * @param integer $id
     * @return mixed
     */
    public function actionFilter($date = null, $code = null)
    {
        self::__checkAuth();

        if (!$date) {
            (date('H') <= 13) ?
            $date = date("Y-m-d", strtotime('-1 days')) :
            $date = date("Y-m-d");
        }
        if (date('N', strtotime($date)) >= 5) {
            $date = date("Y-m-d", strtotime('previous friday', strtotime($date)));
        }

        $data = CurrencyRate::find()->
            where(['date' => $date])->
            andFilterWhere(['code' => $code])->
            all();
        $currencyList = [];

        foreach ($data as $currency) {
            $innerCode                = $currency['code'];
            $innerRate                = $currency['rate'];
            $currencyList[$innerCode] = $innerRate;
        }

        if ($data === []) {
            return ['message' => 'No Currency data available for chosen day'];
        } else {
            if (count($currencyList) > 1) {
                return ['date' => $date, 'rate' => $currencyList];
            } else {
                return ['date' => $date, strtoupper($code) => $currencyList[strtoupper($code)]];
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

    /**
     * Helper Functions
     */

    private function __checkAuth()
    {
        $result = array();

        if (isset($_SERVER["PHP_AUTH_USER"]) && isset($_SERVER["PHP_AUTH_PW"])) { // PHP_AUTH
            $apiUser = $_SERVER["PHP_AUTH_USER"];
            $apiKey  = $_SERVER["PHP_AUTH_PW"];
        } elseif (isset($_SERVER["HTTP_AUTHORIZATION"])) { // HTTP_AUTHORIZATION
            if (preg_match('/Basic\s+(.*)$/i', $_SERVER["HTTP_AUTHORIZATION"], $matches)) {
                list($apiUser, $apiKey) = explode(':', base64_decode($matches[1]));
            }
        } elseif (isset($_SERVER["REDIRECT_HTTP_AUTHORIZATION"])) { // REDIRECT_HTTP_AUTHORIZATION
            if (preg_match('/Basic\s+(.*)$/i', $_SERVER["REDIRECT_HTTP_AUTHORIZATION"], $matches)) {
                list($apiUser, $apiKey) = explode(':', base64_decode($matches[1]));
            }
        } elseif (isset($_SERVER["REDIRECT_REDIRECT_HTTP_AUTHORIZATION"])) { // REDIRECT_REDIRECT_HTTP_AUTHORIZATION
            if (preg_match('/Basic\s+(.*)$/i', $_SERVER["REDIRECT_REDIRECT_HTTP_AUTHORIZATION"], $matches)) {
                list($apiUser, $apiKey) = explode(':', base64_decode($matches[1]));
            }
        }

        if (isset($apiUser) && isset($apiKey) && (self::getKey($apiUser, $apiKey))) {
            $result['status'] = 'success';
            return true;
        } else {
            $result['status']  = 'error';
            $result['message'] = Yii::t('app', 'Invalid credentials!');
            $this->__sendResponse('401', $result);
        }
    }

    private function __sendResponse($status = 200, $body = '', $contentType = 'application/json')
    {
        // Header
        header('HTTP/1.1 ' . $status . ' ' . $this->__getStatusCodeMessage($status));
        header('Content-type: ' . $contentType);
        echo Json::encode($body);
        Yii::$app->end();
    }

    public static function getKey($user, $key)
    {
        try {
            $keys = Yii::$app->params['keys'];
        } catch (Exception $e) {
            $keys = null;
        }
        return ($key === $keys[$user]) ? true : false;
    }

    private function __getStatusCodeMessage($status)
    {
        $codes = array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

}

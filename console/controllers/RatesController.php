<?php

namespace app\console\controllers;

use app\helpers\GetRates;
use yii\console\Controller;

/**
 * Function to bring Daily Rates from the BNR website and save them to the local database
 */
class RatesController extends Controller
{
    public function actionIndex()
    {
        echo "cron service running";
    }

    public function actionDaily()
    {
        GetRates::getDaily();
    }
}

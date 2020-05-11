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

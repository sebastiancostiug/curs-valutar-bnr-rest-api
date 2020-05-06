<?php

namespace app\helpers;

use app\models\CurrencyRate;
use Yii;

class GetRates
{

    /**
     * Get official exchange rates from BNR
     * and parse them into usable array
     **/
    private static function __fetchXml($period, $year = null)
    {
        $xml = new \DOMDocument();

        if ($period === "daily") {
            $url = "https://bnr.ro/nbrfxrates.xml";
        } elseif ($period === "tendays") {
            $url = "https://bnr.ro/nbrfxrates10days.xml";
        } else {
            $url = "https://bnr.ro/files/xml/years/nbrfxrates" . $year . ".xml";
        }

        $myList = ($xml->load($url) !== false) ? $xml->getElementsByTagName("Cube") : "not valid xml";

        return $myList;
    }

    /**
     * Delete all entries in the database
     * that match the specified year
     */
    private static function __deleteEntries($period)
    {
        $deleteEntries = Yii::$app->db->createCommand("DELETE FROM {{exchange}} WHERE YEAR(date)=:year;");
        $deleteEntries->bindValue(':year', $period);
        $deleteEntries->execute();
    }

    /**
     * Fetches daily exchange rates from BNR using the
     * fetchXML() method and writes them in the db
     */
    public function getDaily()
    {
        $xml = self::__fetchXml('daily');

        if (is_string($xml)) {
            var_dump($xml);
        } else {
            $dailyInfo      = $xml[0]->getElementsByTagName("Rate");
            $skippedCount   = 0;
            $addedCount     = 0;
            $processedCount = 0;
            $messages       = [];
            foreach ($dailyInfo as $rate) {
                $existingValue = CurrencyRate::find()->
                    where(['date' => $xml[0]->attributes[0]->value])->
                    andFilterWhere(['code' => $rate->attributes[0]->value])->
                    one();
                $processedCount++;
                if (!$existingValue) {
                    $model             = new CurrencyRate();
                    $model->code       = $rate->attributes[0]->value;
                    $model->rate       = $rate->nodeValue;
                    $model->date       = $xml[0]->attributes[0]->value;
                    $model->multiplier = $rate->attributes[1] ? $rate->attributes[1]->value : 1;
                    // var_dump($model->code, $model->rate, $model->date, $model->multiplier);

                    if ($model->save()) {
                        $response['isSuccess'] = 201;
                        $response['message']   = 'Currency rates added';
                        array_push($messages, $response['message']);
                        $addedCount++;
                    } else {
                        $response['hasErrors'] = $model->hasErrors();
                        $response['errors']    = $model->getErrors();
                        array_push($messages, $response['errors']);
                    }
                } else {
                    $skippedCount++;
                }
            }
            return [
                'Processed Entries'               => ($messages === []) ? $processedCount : $messages,
                'New Entries added to DB'         => $addedCount,
                'Skipped Entries - already in DB' => $skippedCount,
            ];
        }
    }

    /**
     * Fetches last 10 days of exchange rates from BNR using
     * the __fetchXml() method checks for any that already
     * exist in the db and writes the ones missing in the db
     */
    public function getLastTenDays()
    {
        $myList  = self::__fetchXml('tendays');
        $tendays = [];

        if (is_string($myList)) {
            var_dump($myList);
        } else {
            $skippedCount   = 0;
            $processedCount = 0;
            $addedCount     = 0;
            foreach ($myList as $day) {
                $dailyInfo = $day->getElementsByTagName("Rate");
                foreach ($dailyInfo as $item) {
                    $existingValue = CurrencyRate::find()->
                        where(['date' => $day->attributes[0]->value])->
                        andFilterWhere(['code' => $item->attributes[0]->value])->
                        one();
                    $processedCount++;
                    if (!$existingValue) {
                        $code               = $item->attributes[0]->value;
                        ${"currency-$code"} = [];
                        $rate               = floatval($item->nodeValue);
                        $date               = $day->attributes[0]->value;
                        $multiplier         = $item->attributes[1] ? intval($item->attributes[1]->value) : 1;

                        array_push(${"currency-$code"}, $code, $multiplier, $rate, $date);
                        array_push($tendays, ${"currency-$code"});
                        $addedCount++;
                    } else {
                        $skippedCount++;
                    }
                }
            }
            // var_dump($tendays);

            $addMissingDays = Yii::$app->db->createCommand()->batchInsert('{{exchange}}',
                ['code', 'multiplier', 'rate', 'date'],
                $tendays);
            $addMissingDays->execute();
            return [
                'Processed Entries'               => $processedCount,
                'New Entries added to DB'         => $addedCount,
                'Skipped Entries - already in DB' => $skippedCount,
            ];
        }
    }

    /* Delets the exchange rates found in the db for the specified year using __deleteEntries()
    Fetches new yearly exchange rates from BNR for the specified year using the __fetchXml()
    and writes them in the db*/
    public function getYearly($year)
    {
        $myList = self::__fetchXml('yearly', $year);

        if (is_string($myList)) {
            var_dump($myList);
        } else {
            self::__deleteEntries($year);

            ${"year-$year"} = [];
            $addedCount     = 0;

            foreach ($myList as $day) {
                $dailyInfo = $day->getElementsByTagName("Rate");
                foreach ($dailyInfo as $item) {
                    $code               = $item->attributes[0]->value;
                    ${"currency-$code"} = [];
                    $rate               = floatval($item->nodeValue);
                    $date               = $day->attributes[0]->value;
                    $multiplier         = $item->attributes[1] ? intval($item->attributes[1]->value) : 1;

                    array_push(${"currency-$code"}, $code, $multiplier, $rate, $date);
                    array_push(${"year-$year"}, ${"currency-$code"});
                    $addedCount++;
                }
            }

            $addNewYearlyEntries = Yii::$app->db->createCommand()->batchInsert('{{exchange}}',
                ['code', 'multiplier', 'rate', 'date'],
                ${"year-$year"});
            $addNewYearlyEntries->execute();
            // var_dump(${"year-$year"});
            return [
                'Entries written in DB' => $addedCount,
            ];

        }
    }
}

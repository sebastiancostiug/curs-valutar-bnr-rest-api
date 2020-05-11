<?php

namespace app\controllers;

use app\models\CurrencyRate;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Controller;
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
        $data = ['Site index - Nothing here'];

        return $data;
    }
}

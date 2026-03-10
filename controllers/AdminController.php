<?php

namespace app\controllers;

use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\ShortUrl;

class AdminController extends Controller
{
    public function actionLinks()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ShortUrl::find()->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('links', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionLogs($id)
    {
        $model = ShortUrl::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Ссылка не найдена.');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getHits()->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $this->render('logs', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }
}


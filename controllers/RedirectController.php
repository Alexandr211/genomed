<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\ShortUrl;
use app\models\ShortUrlHit;

class RedirectController extends Controller
{
    public function actionGo($code)
    {
        $model = ShortUrl::findOne(['code' => $code]);
        if ($model === null) {
            throw new NotFoundHttpException('Ссылка не найдена.');
        }

        $model->updateCounters(['hit_count' => 1]);

        $hit = new ShortUrlHit();
        $hit->short_url_id = $model->id;
        $hit->ip = Yii::$app->request->userIP ?? 'unknown';
        $hit->created_at = time();
        $hit->save(false);

        return $this->redirect($model->original_url);
    }
}


<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var \app\models\ShortUrl $model */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Логи переходов по ссылке #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Админ: короткие ссылки', 'url' => ['admin/links']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-logs">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <strong>Короткая ссылка:</strong>
        <?php $shortUrl = Url::to(['redirect/go', 'code' => $model->code], true); ?>
        <?= Html::a($shortUrl, $shortUrl, ['target' => '_blank', 'rel' => 'noopener noreferrer']); ?>
    </p>
    <p>
        <strong>Оригинальный URL:</strong>
        <?= Html::a(Html::encode($model->original_url), $model->original_url, [
            'target' => '_blank',
            'rel' => 'noopener noreferrer',
        ]); ?>
    </p>
    <p>
        <strong>Всего переходов:</strong> <?= Html::encode($model->hit_count); ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-bordered table-hover align-middle'],
        'columns' => [
            'id',
            [
                'attribute' => 'ip',
                'label' => 'IP',
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Дата/время',
                'value' => function ($hit) {
                    return date('d.m.Y H:i:s', $hit->created_at);
                },
            ],
        ],
    ]); ?>
</div>


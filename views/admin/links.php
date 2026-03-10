<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Админ: короткие ссылки';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-links">
    <h1><?= Html::encode($this->title) ?></h1>

    <p class="text-muted mb-3">
        Список всех сгенерированных коротких ссылок и их статистика переходов.
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-bordered table-hover align-middle'],
        'columns' => [
            'id',
            [
                'attribute' => 'code',
                'label' => 'Код',
                'value' => function ($model) {
                    /** @var \app\models\ShortUrl $model */
                    return $model->code;
                },
            ],
            [
                'label' => 'Короткая ссылка',
                'format' => 'raw',
                'value' => function ($model) {
                    /** @var \app\models\ShortUrl $model */
                    $shortUrl = Url::to(['redirect/go', 'code' => $model->code], true);
                    return Html::a($shortUrl, $shortUrl, ['target' => '_blank', 'rel' => 'noopener noreferrer']);
                },
            ],
            [
                'attribute' => 'original_url',
                'label' => 'Оригинальный URL',
                'format' => 'raw',
                'value' => function ($model) {
                    /** @var \app\models\ShortUrl $model */
                    return Html::a(Html::encode($model->original_url), $model->original_url, [
                        'target' => '_blank',
                        'rel' => 'noopener noreferrer',
                    ]);
                },
            ],
            [
                'attribute' => 'hit_count',
                'label' => 'Переходов',
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Создано',
                'value' => function ($model) {
                    /** @var \app\models\ShortUrl $model */
                    return date('d.m.Y H:i:s', $model->created_at);
                },
            ],
            [
                'attribute' => 'updated_at',
                'label' => 'Обновлено',
                'value' => function ($model) {
                    /** @var \app\models\ShortUrl $model */
                    return date('d.m.Y H:i:s', $model->updated_at);
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{logs}',
                'buttons' => [
                    'logs' => function ($url, $model) {
                        /** @var \app\models\ShortUrl $model */
                        $url = ['admin/logs', 'id' => $model->id];
                        return Html::a('Логи', $url, ['class' => 'btn btn-sm btn-outline-primary']);
                    },
                ],
            ],
        ],
    ]); ?>
</div>


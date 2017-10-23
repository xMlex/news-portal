<?php

use app\models\User;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success create link-as-dialog']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'headerOptions' => ['style' => 'width:80px'],
            ],
            [
                'attribute' => 'login',
                'filter' => Select2::widget([ //TODO Переделать на ajax
                    'model' => $searchModel,
                    'attribute' => 'login',
                    'data' => \yii\helpers\ArrayHelper::map(User::find()->all(), 'login', 'login'),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])
            ],
            'email:email',
            [
                'attribute' => 'is_active',
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'is_active',
                    'data' => ['0' => 'Нет', '1' => 'Да'],
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]),
                'format' => 'raw',
                'value' => function ($model, $id) {
                    return Html::a($model->is_active ? 'Заблокировать' : 'Активировать', ['toggle-active', 'id' => $id], [
                        'class' => 'btn link-toggle-active ' . ($model->is_active ? 'btn-success' : 'btn-danger'),
                        'data-active' => $model->is_active
                    ]);
                }
            ],
            [
                'attribute' => 'created_at',
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created_at',
                    'convertFormat' => true,
                    'language' => 'ru',
                    'pluginOptions' => [
                        'timePicker' => true,
                        'locale' => [
                            'format' => 'Y-m-d H:i'
                        ],
                    ],
                ]),
            ],
            [
                'attribute' => 'logged_at',
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'logged_at',
                    'convertFormat' => true,
                    'language' => 'ru',
                    'pluginOptions' => [
                        'timePicker' => true,
                        'locale' => [
                            'format' => 'Y-m-d H:i'
                        ],
                    ],
                ]),
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        return Html::a(Html::icon('pencil'), ['update', 'id' => $key], ['class' => 'link-as-dialog']);
                    }
                ]
            ],
        ],
    ]); ?>
</div>

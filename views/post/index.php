<?php

use app\models\User;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PostsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Новости';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="posts-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Yii::$app->user->can('createPost') ? Html::a('Добавить', ['create'], ['class' => 'btn btn-success create link-as-dialog']) : '' ?>
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
                'attribute' => 'user_id',
                'value' => 'user.login',
                'label' => 'Автор',
                'filter' => Select2::widget([ //TODO Переделать на ajax
                    'model' => $searchModel,
                    'attribute' => 'user_id',
                    'data' => \yii\helpers\ArrayHelper::map(User::findActive()->all(), 'id', 'login'),
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])
            ],
            'title',
            'description:ntext',
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
//            'post:ntext',
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

            // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        return Yii::$app->user->can('updatePost', ['post' => $model]) ? Html::a(Html::icon('pencil'), ['update', 'id' => $key], ['class' => 'link-as-dialog']) : '';
                    },
                    'view' => function ($url, $model, $key) {
                        return Yii::$app->user->can('viewPost', ['post' => $model]) ? Html::a(Html::icon('eye-open'), ['view', 'id' => $key]) : '';
                    },
                    'delete' => function ($url, $model, $key) {
                        return Yii::$app->user->can('deletePost', ['post' => $model]) ? Html::a(Html::icon('trash'), ['delete', 'id' => $key], [
                            'data' => [
                                'confirm' => 'Подтвердите удаление?',
                                'method' => 'post',
                            ],
                        ]) : '';
                    },
                ]
            ],
        ],
    ]); ?>
</div>

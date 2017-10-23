<?php
/**
 * Created by Maxim Novikov
 * Date: 19.10.17 15:35
 */

use yii\bootstrap\Html;

/** @var \app\models\User $user */
/** @var \app\models\Posts $post */
?>
<p>Уважаемый <?= $user->login ?>, на сайте "<?= Yii::$app->name ?>" добавлена новость
    <?= Html::a($post->title, $post->getStaticUrl()) ?></p>
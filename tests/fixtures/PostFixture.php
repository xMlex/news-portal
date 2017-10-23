<?php
/**
 * Created by Maxim Novikov
 * Date: 20.10.17 11:13
 */

namespace app\tests\fixtures;


use yii\test\ActiveFixture;

class PostFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Posts';
    public $dataFile = '@app/tests/fixtures/data/post.php';
}
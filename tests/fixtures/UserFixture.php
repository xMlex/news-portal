<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

/**
 * Created by Maxim Novikov
 * Date: 20.10.17 10:39
 */
class UserFixture extends ActiveFixture
{
    public $modelClass = 'app\models\User';
    public $dataFile = '@app/tests/fixtures/data/user.php';
}
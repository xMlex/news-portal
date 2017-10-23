<?php
namespace tests\models;

use app\models\PostNotifications;
use app\models\Posts;
use app\models\User;
use app\tests\fixtures\PostFixture;
use app\tests\fixtures\UserFixture;
use yii\codeception\DbTestCase;
use yii\db\ActiveRecord;

class PostTest extends DbTestCase
{
    public function fixtures()
    {
        return [
            'user' => UserFixture::className(),
            'post' => PostFixture::className(),
        ];
    }

    public function testNotification()
    {
        expect_that($model = Posts::findOne(1));
        $model->trigger(ActiveRecord::EVENT_AFTER_INSERT);

        expect_that($notification = PostNotifications::findOne(['post_id' => $model->id]));
        PostNotifications::deleteAll();
    }

    public function testFindById()
    {
        expect_that($model = Posts::findOne(1));
        expect($model->title)->equals('Тестовая новость1');

        expect_not(User::findIdentity(999));
    }

    public function testFindByUserId()
    {
        expect_that($model = Posts::findOne(2));
        expect($model->user_id)->equals(2);

        expect_not(Posts::findOne(['user_id' => 999]));
    }

    /**
     * @depends testFindById
     */
    public function testValidatePost($user)
    {
        expect_that($model = Posts::findOne(2));
        expect($model->title)->equals('Тестовая новость2');
        expect($model->description)->equals('Краткое описание');
        expect($model->post)->equals('Полное описание тестовой новости');
    }

}

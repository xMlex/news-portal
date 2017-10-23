<?php
/**
 * Created by IntelliJ IDEA.
 * User: mlex
 * Date: 18.10.17
 * Time: 15:52
 */

namespace app\config;


use yii\base\BootstrapInterface;
use yii\web\Application;
use yii\web\User;

class Bootstrap implements BootstrapInterface
{

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $app->user->on(User::EVENT_AFTER_LOGIN, ['app\models\User', 'afterLogin']);
    }
}
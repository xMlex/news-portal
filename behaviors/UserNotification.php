<?php
/**
 * Created by Maxim Novikov
 * Date: 19.10.17 15:09
 */

namespace app\behaviors;


use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class UserNotification
 * @package app\behaviors
 * @property $owner yii\db\ActiveRecord
 */
class UserNotification extends Behavior
{

    public $fieldEmail = 'email';
    public $fieldPassword = 'password';

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterCreate',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
        ];
    }

    public function afterCreate($event)
    {
        \Yii::$app->mailer->compose('registration-link', ['user' => $this->owner])
            ->setTo($this->owner->{$this->fieldEmail})
            ->setSubject('Спасибо за регистрацию, подтвердите ваш E-Mail')
            ->send();
    }

    public function beforeUpdate($event)
    {
        if ($this->owner instanceof ActiveRecord && !$this->owner->isNewRecord && $this->owner->isAttributeChanged($this->fieldPassword)) {
            \Yii::$app->mailer->compose('password-changed', ['user' => $this->owner])
                ->setTo($this->owner->{$this->fieldEmail})
                ->setSubject('Изиенение пароля')
                ->send();
        }
    }
}
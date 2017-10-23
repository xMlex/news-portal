<?php
/**
 * Created by Maxim Novikov
 * Date: 19.10.17 16:30
 */

namespace app\behaviors;


use app\models\PostNotifications;
use app\models\User;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class PostNotification extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterCreate',
        ];
    }

    public function afterCreate($event)
    {
        $finsUsers = User::findActive();

        foreach ($finsUsers->batch() as $rows){
            /** @var User[] $rows*/
            foreach ($rows as $user){
                switch ($user->post_notification_type){
                    case User::POST_NOTIFICATION_MESSAGE:
                        $notification = new PostNotifications();
                        $notification->user_id = $user->id;
                        $notification->post_id = $this->owner->id;
                        $notification->save();
                        break;
                    case User::POST_NOTIFICATION_EMAIL:
                        //TODO Необходимо ставить в очередь. Иначе при возрастании числа пользователей - будет тупить нереально и в итоге завалится
                        \Yii::$app->mailer->compose('new-post', ['post' => $this->owner, 'user' => $user])
                            ->setTo($user->email)
                            ->setSubject('Добавлена новость')
                            ->send();
                        break;
                }
            }
        }
    }
}
<?php

namespace app\roles;


use app\models\Posts;
use yii\rbac\Rule;

class AuthorRole extends Rule
{
    public $name = 'manager';

    /**
     * Executes the rule.
     *
     * @param string|int $user the user ID. This should be either an integer or a string representing
     * the unique identifier of a user. See [[\yii\web\User::id]].
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to [[CheckAccessInterface::checkAccess()]].
     * @return bool a value indicating whether the rule permits the auth item it is associated with.
     */
    public function execute($user, $item, $params)
    {
        if (\Yii::$app->user->can('administration')) {
            return true;
        }

        if (!empty($params['post'])) {
            return isset($params['post']) ? $params['post']->user_id == $user : false;
        }

        if (!empty($params['postId'])) {
            $post = Posts::findOne($params['postId']);
            return empty($post) ? false : $post->user_id == $user;
        }
    }
}
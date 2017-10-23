<?php

use yii\db\Migration;

class m171017_133539_init extends Migration
{
    public function up()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'login' => $this->string(32)->unique()->notNull()->comment('Имя пользователя'),
            'email' => $this->string(80)->unique()->notNull()->comment('E-Mail'),
            'password' => $this->string(32)->comment('Пароль'),
            'access_token' => $this->string(32)->notNull()->comment('Код доступа'),
            'password_reset_code' => $this->string(32)->comment('Ключ сброса пароля'),
            'is_validated' => $this->boolean()->defaultValue(0)->comment('Пользователь подтвердил email'),
            'is_active' => $this->boolean()->defaultValue(1)->comment('Активен ли пользователь'),
            'post_notification_type' => $this->integer()->defaultValue(0)->comment('Уведопление о новых новостях'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('Дата/время создания'),
            'logged_at' => $this->timestamp()->comment('Дата/время последней авторизации'),
        ]);

        $this->createIndex('idx-users-id', '{{%users}}', 'id');
        $this->createIndex('idx-users-login', '{{%users}}', 'login');
        $this->createIndex('idx-users-email', '{{%users}}', 'email');
        $this->createIndex('idx-users-created', '{{%users}}', 'created_at');
        $this->createIndex('idx-users-logged', '{{%users}}', 'logged_at');

        $this->createTable('{{%posts}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->comment('Автор'),
            'title' => $this->string(255)->unique()->notNull()->comment('Заголовок новости'),
            'description' => $this->text()->notNull()->comment('Краткое описание новости'),
            'post' => $this->text()->notNull()->comment('Полная новость'),
            'photo' => $this->string(255)->comment('Картинка новости'),
            'is_active' => $this->boolean()->defaultValue(0)->comment('Показывать новость'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('Дата/время создания'),
            'updated_at' => $this->timestamp()->comment('Дата/время обновления'),
        ]);

        $this->createIndex('idx-post-id', '{{%posts}}', 'id');
        $this->createIndex('idx-post-user-id', '{{%posts}}', 'user_id');
        $this->addForeignKey('fk-posts-user-id', '{{%posts}}', 'user_id', '{{%users}}', 'id', 'CASCADE');

        $this->createTable('{{%post_notifications}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->comment('Пользователь'),
            'post_id' => $this->integer()->notNull()->comment('Новость'),
            'is_read' => $this->boolean()->defaultValue(0)->comment('Прочитано')
        ]);
        $this->createIndex('idx-post-ntfs-id', '{{%post_notifications}}', 'id');
        $this->createIndex('idx-post-ntfs-is-read', '{{%post_notifications}}', 'is_read');

        $this->addForeignKey('fk-post-ntfs-user', '{{%post_notifications}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-post-ntfs-post', '{{%post_notifications}}', 'post_id', '{{%posts}}', 'id', 'CASCADE', 'CASCADE');

        $auth = Yii::$app->authManager;

        $managerRole = new \app\roles\AuthorRole();
        $auth->add($managerRole);

        $createPost = $auth->createPermission('createPost');
        $createPost->description = 'Добавление новостей';
        $auth->add($createPost);

        $updatePost = $auth->createPermission('updatePost');
        $updatePost->description = 'Изменение новостей';
        $updatePost->ruleName = $managerRole->name;
        $auth->add($updatePost);

        $viewPost = $auth->createPermission('viewPost');
        $viewPost->description = 'Просмотр новостей';
        $auth->add($viewPost);

        $deletePost = $auth->createPermission('deletePost');
        $deletePost->description = 'Удаление новостей';
        $deletePost->ruleName = $managerRole->name;
        $auth->add($deletePost);

        $administration = $auth->createPermission('administration');
        $administration->description = 'Управление сайтом';
        $auth->add($administration);

        // Авторизованный пользователь может просматривать полные новости
        $user = $auth->createRole('user');
        $user->description = 'Авторизованный пользователь может просматривать полные новости';
        $auth->add($user);
        $auth->addChild($user, $viewPost);

        // Менеджер может добавлять новости и редактировать/удалять только свои новости
        $manager = $auth->createRole('manager');
        $manager->description = 'Менеджер может добавлять новости и редактировать/удалять только свои новости';
        $auth->add($manager);
        $auth->addChild($manager, $user);
        $auth->addChild($manager, $createPost);
        $auth->addChild($manager, $updatePost);
        $auth->addChild($manager, $deletePost);

        $admin = $auth->createRole('admin');
        $admin->description = 'Администратор';
        $auth->add($admin);
        $auth->addChild($admin, $manager);
        $auth->addChild($admin, $administration);
    }

    public function down()
    {
        $this->dropTable('{{%post_notifications}}');
        $this->dropTable('{{%posts}}');
        $this->dropTable('{{%users}}');
    }
}

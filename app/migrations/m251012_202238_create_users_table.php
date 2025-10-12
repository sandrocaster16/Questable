<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m251012_202238_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),

            // Добавляем поля для TG
            'tg_id' => $this->string()->unique(),
            'tg_username' => $this->string(),
            'tg_firstname' => $this->string(),
            'tg_lastname' => $this->string(),
            'tg_photourl' => $this->string(),

            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%users}}');
    }
}

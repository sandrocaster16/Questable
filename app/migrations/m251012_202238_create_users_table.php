<?php

use yii\db\Migration;

/**
 * Handles the creation of tables related to quests and users.
 */
class m251012_202238_create_users_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /** -------------------- USERS -------------------- */
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'tg_id' => $this->integer(11)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'deleted_at' => $this->integer()->defaultValue(null),
        ]);

        /** -------------------- QUESTS -------------------- */
        $this->createTable('{{%quests}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'deleted_at' => $this->dateTime()->null(),
        ]);

        /** -------------------- QUESTS.STATIONS -------------------- */
        $this->createTable('{{%quests_stations}}', [
            'id' => $this->primaryKey(),
            'quest_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'deleted_at' => $this->dateTime()->null(),
        ]);

        $this->addForeignKey(
            'fk_quests_stations_quest',
            '{{%quests_stations}}',
            'quest_id',
            '{{%quests}}',
            'id',
            'CASCADE'
        );

        /** -------------------- QUESTS.QUESTIONS -------------------- */
        $this->createTable('{{%quests_questions}}', [
            'id' => $this->primaryKey(),
            'station_id' => $this->integer()->notNull(),
            'question' => $this->string(2048)->notNull(),
            'answer' => $this->json()->notNull(),
            'help' => $this->string(2048)->null(),
            'message' => $this->string(2048)->null(),
            'created_at' => $this->dateTime()->notNull(),
            'deleted_at' => $this->dateTime()->null(),
        ]);

        $this->addForeignKey(
            'fk_quests_questions_station',
            '{{%quests_questions}}',
            'station_id',
            '{{%quests_stations}}',
            'id',
            'CASCADE'
        );

        /** -------------------- QUESTS.USERS -------------------- */
        $this->createTable('{{%quests_users}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'quest_id' => $this->integer()->notNull(),
            'role' => "ENUM('player','owner','volunteer') NOT NULL",
            'command_id' => $this->integer()->null(),
            'points' => $this->integer()->defaultValue(0),
            'banned' => $this->dateTime()->null(),
            'created_at' => $this->dateTime()->notNull(),
        ]);

        $this->addForeignKey(
            'fk_quests_users_user',
            '{{%quests_users}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_quests_users_quest',
            '{{%quests_users}}',
            'quest_id',
            '{{%quests}}',
            'id',
            'CASCADE'
        );

        /** -------------------- USERS (extended) -------------------- */
        $this->addColumn('{{%users}}', 'role', "ENUM('root','admin','user') NOT NULL DEFAULT 'user'");
        $this->addColumn('{{%users}}', 'deleted_at', $this->dateTime()->null());
        $this->addColumn('{{%users}}', 'banned', $this->dateTime()->null());

        // Внешний ключ от users.id → quests_users.user_id (обратная ссылка)
        $this->addForeignKey(
            'fk_users_to_quests_users',
            '{{%users}}',
            'id',
            '{{%quests_users}}',
            'user_id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_users_to_quests_users', '{{%users}}');
        $this->dropForeignKey('fk_quests_users_user', '{{%quests_users}}');
        $this->dropForeignKey('fk_quests_users_quest', '{{%quests_users}}');
        $this->dropForeignKey('fk_quests_questions_station', '{{%quests_questions}}');
        $this->dropForeignKey('fk_quests_stations_quest', '{{%quests_stations}}');

        $this->dropTable('{{%quests_users}}');
        $this->dropTable('{{%quests_questions}}');
        $this->dropTable('{{%quests_stations}}');
        $this->dropTable('{{%quests}}');
        $this->dropTable('{{%users}}');
    }
}

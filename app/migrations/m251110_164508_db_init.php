<?php

use yii\db\Migration;


class m251110_164508_db_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Таблица пользователей
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull(),
            'avatar_url' => $this->string(2048)->defaultValue(null),
            // Yii2 не имеет встроенного типа ENUM, рекомендуется использовать строки/константы в коде
            'role' => "ENUM('root', 'admin', 'user') NOT NULL DEFAULT 'user'",
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'deleted_at' => $this->dateTime()->defaultValue(null),
            'banned' => $this->dateTime()->defaultValue(null),
        ]);

        // Таблица аутентификации пользователей
        $this->createTable('{{%user_authentication}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'source' => "ENUM('telegram', 'email') NOT NULL",
            'identifier' => $this->string(255)->notNull(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Таблица квестов
        $this->createTable('{{%quests}}', [
            'id' => $this->primaryKey(),
            'creator_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text()->defaultValue(null),
            'cover_image_url' => $this->string(2048)->defaultValue(null),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'delete_at' => $this->dateTime()->defaultValue(null), // Имя поля как в вашей схеме
        ]);

        // Таблица станций квеста
        $this->createTable('{{%quest_stations}}', [
            'id' => $this->primaryKey(),
            'quest_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
            'type' => "ENUM('info', 'quiz', 'curator_check') NOT NULL",
            'content' => $this->text()->defaultValue(null),
            'options' => $this->json()->defaultValue(null),
            'qr_identifier' => $this->string(255)->notNull()->unique(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'deleted_at' => $this->dateTime()->defaultValue(null),
        ]);

        // Таблица команд квеста
        $this->createTable('{{%quest_teams}}', [
            'id' => $this->primaryKey(),
            'quest_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
            'leader_id' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Таблица участников квеста
        $this->createTable('{{%quest_participants}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'quest_id' => $this->integer()->notNull(),
            'team_id' => $this->integer()->defaultValue(null),
            'role' => "ENUM('owner', 'volunteer', 'player') NOT NULL",
            'points' => $this->integer()->notNull()->defaultValue(0),
            'banned' => $this->dateTime()->defaultValue(null),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Таблица прогресса по станциям
        $this->createTable('{{%station_progress}}', [
            'id' => $this->primaryKey(),
            'participant_id' => $this->integer()->notNull(),
            'station_id' => $this->integer()->notNull(),
            'status' => "ENUM('pending', 'completed') NOT NULL DEFAULT 'pending'",
            'completed_at' => $this->dateTime()->defaultValue(null),
        ]);

        // --- Добавление внешних ключей ---

        // Для user_authentication
        $this->addForeignKey('fk-auth_user-user_id', '{{%user_authentication}}', 'user_id', '{{%users}}', 'id', 'CASCADE');

        // Для quests
        $this->addForeignKey('fk-quest_creator-creator_id', '{{%quests}}', 'creator_id', '{{%users}}', 'id', 'RESTRICT');

        // Для quest_stations
        $this->addForeignKey('fk-station_quest-quest_id', '{{%quest_stations}}', 'quest_id', '{{%quests}}', 'id', 'CASCADE');

        // Для quest_teams
        $this->addForeignKey('fk-team_quest-quest_id', '{{%quest_teams}}', 'quest_id', '{{%quests}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-team_leader-leader_id', '{{%quest_teams}}', 'leader_id', '{{%users}}', 'id', 'RESTRICT');

        // Для quest_participants
        $this->addForeignKey('fk-participant_user-user_id', '{{%quest_participants}}', 'user_id', '{{%users}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-participant_quest-quest_id', '{{%quest_participants}}', 'quest_id', '{{%quests}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-participant_team-team_id', '{{%quest_participants}}', 'team_id', '{{%quest_teams}}', 'id', 'SET NULL');

        // Для station_progress
        $this->addForeignKey('fk-progress_participant-participant_id', '{{%station_progress}}', 'participant_id', '{{%quest_participants}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-progress_station-station_id', '{{%station_progress}}', 'station_id', '{{%quest_stations}}', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // --- Удаление внешних ключей ---
        // (в обратном порядке от создания)
        $this->dropForeignKey('fk-progress_station-station_id', '{{%station_progress}}');
        $this->dropForeignKey('fk-progress_participant-participant_id', '{{%station_progress}}');
        $this->dropForeignKey('fk-participant_team-team_id', '{{%quest_participants}}');
        $this->dropForeignKey('fk-participant_quest-quest_id', '{{%quest_participants}}');
        $this->dropForeignKey('fk-participant_user-user_id', '{{%quest_participants}}');
        $this->dropForeignKey('fk-team_leader-leader_id', '{{%quest_teams}}');
        $this->dropForeignKey('fk-team_quest-quest_id', '{{%quest_teams}}');
        $this->dropForeignKey('fk-station_quest-quest_id', '{{%quest_stations}}');
        $this->dropForeignKey('fk-quest_creator-creator_id', '{{%quests}}');
        $this->dropForeignKey('fk-auth_user-user_id', '{{%user_authentication}}');

        // --- Удаление таблиц ---
        // (в обратном порядке от создания)
        $this->dropTable('{{%station_progress}}');
        $this->dropTable('{{%quest_participants}}');
        $this->dropTable('{{%quest_teams}}');
        $this->dropTable('{{%quest_stations}}');
        $this->dropTable('{{%quests}}');
        $this->dropTable('{{%user_authentication}}');
        $this->dropTable('{{%users}}');
    }
}
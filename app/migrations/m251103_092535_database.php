<?php

use yii\db\Migration;
use yii\db\mysql\Schema;

class m251103_092535_database extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // Таблица users
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull(),
            'avatar_url' => $this->string(2048)->defaultValue(null),
            'role' => $this->enum(['root', 'admin', 'user'])->notNull()->defaultValue('user'),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'deleted_at' => $this->dateTime()->defaultValue(null),
            'banned' => $this->dateTime()->defaultValue(null),
        ], $tableOptions);

        // Таблица user_authentication
        $this->createTable('{{%user_authentication}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'source' => $this->enum(['telegram', 'email'])->notNull(),
            'identifier' => $this->string(255)->notNull(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->addForeignKey(
            'fk_auth_user',
            '{{%user_authentication}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Таблица quests
        $this->createTable('{{%quests}}', [
            'id' => $this->primaryKey(),
            'creator_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text()->defaultValue(null),
            'cover_image_url' => $this->string(2048)->defaultValue(null),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'delete_at' => $this->dateTime()->defaultValue(null),
        ], $tableOptions);

        $this->addForeignKey(
            'fk_quest_creator',
            '{{%quests}}',
            'creator_id',
            '{{%users}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        // Таблица quest_stations
        $this->createTable('{{%quest_stations}}', [
            'id' => $this->primaryKey(),
            'quest_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
            'type' => $this->enum(['info', 'quiz', 'curator_check'])->notNull(),
            'content' => $this->text()->defaultValue(null),
            'options' => $this->json()->defaultValue(null),
            'qr_identifier' => $this->string(255)->notNull(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'deleted_at' => $this->dateTime()->defaultValue(null),
        ], $tableOptions);

        $this->addForeignKey(
            'fk_station_quest',
            '{{%quest_stations}}',
            'quest_id',
            '{{%quests}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex(
            'idx_qr_identifier',
            '{{%quest_stations}}',
            'qr_identifier',
            true
        );

        // Таблица quest_teams
        $this->createTable('{{%quest_teams}}', [
            'id' => $this->primaryKey(),
            'quest_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
            'leader_id' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->addForeignKey(
            'fk_team_quest',
            '{{%quest_teams}}',
            'quest_id',
            '{{%quests}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_team_leader',
            '{{%quest_teams}}',
            'leader_id',
            '{{%users}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        // Таблица quest_participants
        $this->createTable('{{%quest_participants}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'quest_id' => $this->integer()->notNull(),
            'team_id' => $this->integer()->defaultValue(null),
            'role' => $this->enum(['owner', 'volunteer', 'player'])->notNull(),
            'points' => $this->integer()->notNull()->defaultValue(0),
            'banned' => $this->dateTime()->defaultValue(null),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->addForeignKey(
            'fk_participant_user',
            '{{%quest_participants}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_participant_quest',
            '{{%quest_participants}}',
            'quest_id',
            '{{%quests}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_participant_team',
            '{{%quest_participants}}',
            'team_id',
            '{{%quest_teams}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // Таблица station_progress
        $this->createTable('{{%station_progress}}', [
            'id' => $this->primaryKey(),
            'participant_id' => $this->integer()->notNull(),
            'station_id' => $this->integer()->notNull(),
            'status' => $this->enum(['pending', 'completed'])->notNull()->defaultValue('pending'),
            'completed_at' => $this->dateTime()->defaultValue(null),
        ], $tableOptions);

        $this->addForeignKey(
            'fk_progress_participant',
            '{{%station_progress}}',
            'participant_id',
            '{{%quest_participants}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_progress_station',
            '{{%station_progress}}',
            'station_id',
            '{{%quest_stations}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Удаление таблиц в обратном порядке для избежания ошибок foreign key
        $this->dropTable('{{%station_progress}}');
        $this->dropTable('{{%quest_participants}}');
        $this->dropTable('{{%quest_teams}}');
        $this->dropTable('{{%quest_stations}}');
        $this->dropTable('{{%quests}}');
        $this->dropTable('{{%user_authentication}}');
        $this->dropTable('{{%users}}');
    }
}
<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%system_logs}}`.
 */
class m251217_165517_create_system_logs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%system_logs}}', [
            'id' => $this->primaryKey()->unsigned(),
            'type' => $this->string(32)->notNull(),
            'message' => $this->text(),
            'created_at' => $this->dateTime()
                ->notNull()
                ->defaultExpression('CURRENT_TIMESTAMP'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $this->createIndex(
            'idx_system_logs_created_at',
            '{{%system_logs}}',
            'created_at'
        );

        $this->createIndex(
            'idx_system_logs_type',
            '{{%system_logs}}',
            'type'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%system_logs}}');
    }
}

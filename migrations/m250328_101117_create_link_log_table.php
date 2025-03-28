<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%link_log}}`.
 */
class m250328_101117_create_link_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%link_log}}', [
            'id' => $this->primaryKey(),
            'link_id' => $this->integer()->notNull(),
            'ip_address' => $this->string(45),
            'accessed_at' => $this->dateTime()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-link_log-link_id',
            'link_log',
            'link_id',
            'link',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-link_log-link_id', 'link_log');

        $this->dropTable('{{%link_log}}');
    }
}

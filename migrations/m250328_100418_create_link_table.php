<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%links}}`.
 */
class m250328_100418_create_link_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%link}}', [
            'id' => $this->primaryKey(),
            'original_url' => $this->string(512)->notNull(),
            'short_code' => $this->string(10)->notNull()->unique(),
            'created_at' => $this->dateTime()->notNull(),
            'hit_counter' => $this->integer()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%link}}');
    }
}

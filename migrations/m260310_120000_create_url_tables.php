<?php

use yii\db\Migration;

class m260310_120000_create_url_tables extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%short_url}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(16)->notNull()->unique(),
            'original_url' => $this->text()->notNull(),
            'hit_count' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%short_url_hit}}', [
            'id' => $this->primaryKey(),
            'short_url_id' => $this->integer()->notNull(),
            'ip' => $this->string(45)->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk_short_url_hit_short_url',
            '{{%short_url_hit}}',
            'short_url_id',
            '{{%short_url}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_short_url_hit_short_url', '{{%short_url_hit}}');
        $this->dropTable('{{%short_url_hit}}');
        $this->dropTable('{{%short_url}}');
    }
}


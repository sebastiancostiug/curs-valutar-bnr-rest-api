<?php

use yii\db\Migration;

/**
 * Class m200423_000000_create_exchange
 */

class m200423_000000_create_exchange extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /* Table: exchange */
        $this->createTable('{{%exchange}}', [
            'id'         => $this->primaryKey()->unsigned(),
            'code'       => $this->string(3)->notNull(),
            'multiplier' => $this->integer()->notNull()->defaultValue(1),
            'rate'       => $this->double(2)->notNull(),
            'date'       => $this->date()->notNull(),
        ]);

        /* Column names */
        $this->addCommentOnColumn('{{%exchange}}', 'id', 'ID');
        $this->addCommentOnColumn('{{%exchange}}', 'code', 'Currency ISO code');
        $this->addCommentOnColumn('{{%exchange}}', 'multiplier', 'Currency multiplier');
        $this->addCommentOnColumn('{{%exchange}}', 'rate', 'Rate');
        $this->addCommentOnColumn('{{%exchange}}', 'date', 'Date');

        /* Unique index for pairs ['code', 'date'] */
        $this->createIndex('unqExchangeRate', '{{%exchange}}', ['code', 'date'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%exchange}}');
    }
}

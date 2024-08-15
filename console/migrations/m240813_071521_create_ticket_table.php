<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ticket}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m240813_071521_create_ticket_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ticket}}', [
            'id' => $this->string(16)->notNull(),
            'author_id' => $this->integer()->notNull(),
            'title' => $this->string(255)->notNull(),
            'body' => $this->text(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'status' => $this->boolean()
        ]);
        $this->addPrimaryKey('PK_ticket_id','{{%ticket}}','id');
        // creates index for column `author_id`
        $this->createIndex(
            '{{%idx-ticket-author_id}}',
            '{{%ticket}}',
            'author_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-ticket-author_id}}',
            '{{%ticket}}',
            'author_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-ticket-author_id}}',
            '{{%ticket}}'
        );

        // drops index for column `author_id`
        $this->dropIndex(
            '{{%idx-ticket-author_id}}',
            '{{%ticket}}'
        );

        $this->dropTable('{{%ticket}}');
    }
}

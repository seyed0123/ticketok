<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_ticket}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%ticket}}`
 * - `{{%user}}`
 */
class m240813_075755_create_user_ticket_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_ticket}}', [
            'id' => $this->primaryKey(),
            'ticket_id' => $this->string(16)->notNull(),
            'user_id' => $this->integer()->notNull(),
            'status' => $this->boolean()->notNull(),
            'update_at' => $this->integer(10)->notNull(),
        ]);

        // creates index for column `ticket_id`
        $this->createIndex(
            '{{%idx-user_ticket-ticket_id}}',
            '{{%user_ticket}}',
            'ticket_id'
        );

        // add foreign key for table `{{%ticket}}`
        $this->addForeignKey(
            '{{%fk-user_ticket-ticket_id}}',
            '{{%user_ticket}}',
            'ticket_id',
            '{{%ticket}}',
            'id',
            'CASCADE'
        );

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-user_ticket-user_id}}',
            '{{%user_ticket}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-user_ticket-user_id}}',
            '{{%user_ticket}}',
            'user_id',
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
        // drops foreign key for table `{{%ticket}}`
        $this->dropForeignKey(
            '{{%fk-user_ticket-ticket_id}}',
            '{{%user_ticket}}'
        );

        // drops index for column `ticket_id`
        $this->dropIndex(
            '{{%idx-user_ticket-ticket_id}}',
            '{{%user_ticket}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-user_ticket-user_id}}',
            '{{%user_ticket}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-user_ticket-user_id}}',
            '{{%user_ticket}}'
        );

        $this->dropTable('{{%user_ticket}}');
    }
}

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_ticket}}".
 *
 * @property int $id
 * @property string $ticket_id
 * @property int $user_id
 * @property int $status
 * @property int $update_at
 *
 * @property Ticket $ticket
 * @property User $user
 */
class UserTicket extends \yii\db\ActiveRecord
{

    const STATUS_SEEN = 1;
    const STATUS_UNSEEN = 0;
    const STATUS_ANSWER = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_ticket}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ticket_id', 'user_id', 'status', 'update_at'], 'required'],
            [['user_id', 'status', 'update_at'], 'integer'],
            [['ticket_id'], 'string', 'max' => 16],
            [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ticket::class, 'targetAttribute' => ['ticket_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ticket_id' => 'Ticket ID',
            'user_id' => 'User ID',
            'status' => 'Status',
            'update_at' => 'Update At',
        ];
    }

    /**
     * Gets query for [[Ticket]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\TicketQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Ticket::class, ['id' => 'ticket_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\UserTicketQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\UserTicketQuery(get_called_class());
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        $this->update_at = time();
        return parent::save($runValidation, $attributeNames);
    }
}

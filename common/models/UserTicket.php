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

    public static function getStatusLabels(){
        return [
            'UNSeen',
            'Seen',
        ];
    }
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

    public static function handelSave($isNewRecord,$usernames,$ticket_id){
        if($isNewRecord){
            if (!empty($usernames)) {
                $usernameArray = array_map('trim', explode(',', $usernames));
                foreach ($usernameArray as $username) {

                    $user = User::findOne(['username' => $username]);
                    if ($user) {
                        $userTicket = new UserTicket();
                        $userTicket->ticket_id = $ticket_id;
                        $userTicket->user_id = $user->id;
                        $userTicket->status = UserTicket::STATUS_UNSEEN;
                        if (!$userTicket->save()) {
                            Yii::error($userTicket->errors, 'application');
                        }
                    }
                }
            }
        }else if (!empty($usernames)) {
            $usernameArray = array_map('trim', explode(',', $usernames));
            $savedUserTickets = UserTicket::findAll(['ticket_id' =>$ticket_id ]);

            $savedUsernames = [];

            foreach ($savedUserTickets as $userTicket) {
                $user = $userTicket->user;
                if ($user) {
                    $savedUsernames[$user->username] = $userTicket;
                }
            }

            foreach ($usernameArray as $username) {
                $user = User::findOne(['username' => $username]);
                if ($user && !isset($savedUsernames[$username])) {
                    $userTicket = new UserTicket();
                    $userTicket->ticket_id = $ticket_id;
                    $userTicket->user_id = $user->id;
                    $userTicket->status = UserTicket::STATUS_UNSEEN;
                    $userTicket->save();
                }
            }

            foreach ($savedUsernames as $username => $userTicket) {
                if (!in_array($username, $usernameArray, true)) {
                    $userTicket->delete();
                }
            }
        }
    }
}

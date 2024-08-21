<?php

namespace common\models;

use common\models\query\UserTicketQuery;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%ticket}}".
 *
 * @property string $id
 * @property int $author_id
 * @property string $title
 * @property string|null $body
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $status
 *
 * @property User $author
 * @property UserTicket[] $userTickets
 * @property UserTicket[] $myUserTickets
 */
class Ticket extends \yii\db\ActiveRecord
{

    public $usernames;
    CONST STATUS_DRAFT = 0;
    CONST STATUS_SEND = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%ticket}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'author_id', 'title', 'usernames'], 'required'],
            [['author_id', 'created_at', 'updated_at', 'status'], 'integer'],
            [['body'], 'string'],
            [['id'], 'string', 'max' => 16],
            [['title'], 'string', 'max' => 255],
            [['id'], 'unique'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => 'Author ID',
            'title' => 'Title',
            'body' => 'Body',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
        ];
    }

    public function getStatusLabels(){
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SEND => 'Send'
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }

    /**
     * Gets query for [[UserTickets]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserTicketQuery
     */
    public function getUserTickets()
    {
        return $this->hasMany(UserTicket::class, ['ticket_id' => 'id']);
    }

    public function getMyUserTickets()
    {
        return $this->getUserTickets()->andWhere([UserTicket::tableName() . '.user_id' => Yii::$app->user->id]);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\TicketQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\TicketQuery(get_called_class());
    }


    public function handelSave($isSaveNow=False):bool{
        $flag = true;
        if(empty($this->usernames))
        {
            $this->addError($this->usernames, 'Usernames cannot be empty.');
            return false;
        }
        if($isSaveNow){
            if (!empty($this->usernames)) {
                foreach ($this->usernames as $user_id) {
                    $user = User::findIdentity($user_id);
                    if ($user) {
                        $userTicket = new UserTicket();
                        $userTicket->ticket_id = $this->id;
                        $userTicket->user_id = $user->id;
                        $userTicket->status = UserTicket::STATUS_UNSEEN;
                        if (!$userTicket->save()) {
                            Yii::error($userTicket->errors, 'application');
                            $flag = false;
                        }
                    }
                }
            }
        }else if (!empty($this->usernames)) {

            $savedUserTickets = $this->getUserTickets()->all();
            $savedIds = [];
            foreach ($savedUserTickets as $userTicket) {
                $savedIds[] = (string)$userTicket->user->id;
            }


            foreach ($this->usernames as $userId) {
                $user = User::findIdentity($userId);
                if ($user && !in_array($userId, $savedIds, true)) {
                    $userTicket = new UserTicket();
                    $userTicket->ticket_id = $this->id;
                    $userTicket->user_id = $user->id;
                    $userTicket->status = UserTicket::STATUS_UNSEEN;
                    if (!$userTicket->save()) {
                        Yii::error($userTicket->errors, 'application');
                        $flag = false;
                    }
                }
            }

            foreach ($savedIds as $userId) {
                if (!in_array($userId, $this->usernames, true)) {
                    $userTicket = UserTicket::findone(['user_id'=>$userId,'ticket_id'=>$this->id]);
                    if (!$userTicket->delete()) {
                        Yii::error($userTicket->errors, 'application');
                        $flag = false;
                    }
                }
            }
        }
        return $flag;
    }

    public function beforeValidate()
    {
        if($this->isNewRecord){
            $this->id = Yii::$app->security->generateRandomString(16);
            $this->author_id = Yii::$app->user->id;
        }
        return parent::beforeValidate();
    }
}

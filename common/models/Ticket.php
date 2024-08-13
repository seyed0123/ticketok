<?php

namespace common\models;

use Yii;

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
    public function rules()
    {
        return [
            [['id', 'author_id', 'title'], 'required'],
            [['author_id', 'created_at', 'updated_at', 'status'], 'integer'],
            [['body'], 'string'],
            [['id'], 'string', 'max' => 16],
            [['title','usernames'], 'string', 'max' => 255],
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
     * {@inheritdoc}
     * @return \common\models\query\TicketQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\TicketQuery(get_called_class());
    }

    /**
     * @throws \yii\db\Exception
     * @throws \yii\base\Exception
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if($this->isNewRecord){
            $this->id = Yii::$app->security->generateRandomString(16);
            $this->author_id = Yii::$app->user->id;
            $this->created_at = time();
            $this->updated_at = time();
            $usernames = $this->usernames;
            if (!empty($usernames)) {
                $usernameArray = array_map('trim', explode(',', $usernames));
                foreach ($usernameArray as $username) {
                    $user = User::findOne(['username' => $username]);
                    if ($user) {
                        $userTicket = new UserTicket();
                        $userTicket->ticket_id = $this->id;
                        $userTicket->user_id = $user->id;
                        $userTicket->status = UserTicket::STATUS_UNSEEN;
                        $userTicket->update_at = time();
                        $userTicket->save();
                    }
                }
            }
        }else{
            $this->updated_at = time();
        }
        return parent::save($runValidation, $attributeNames);
    }
}

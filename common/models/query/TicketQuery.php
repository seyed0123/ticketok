<?php

namespace common\models\query;

use common\models\Ticket;

/**
 * This is the ActiveQuery class for [[\common\models\Ticket]].
 *
 * @see \common\models\Ticket
 */
class TicketQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \common\models\Ticket[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\Ticket|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byUser($userTickets)
    {
        return $this->andWhere(['author_id'=>$userTickets]);
    }

    public function byActive()
    {
        return $this->andWhere([Ticket::tableName().'.status'=>Ticket::STATUS_SEND]);
    }
}

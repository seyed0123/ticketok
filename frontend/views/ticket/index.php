<?php

use yii\grid\SerialColumn;
use common\models\Ticket;
use common\models\UserTicket;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var common\models\TicketSearch $searchModel */

$this->title = 'Inbox Tickets';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ticket-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Ticket', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function (UserTicket $model, $key, $index, $grid) {
            if ($model->status === UserTicket::STATUS_SEEN) {
                return ['class' => 'table-secondary opacity-25'];
            } else {
                return ['class' => 'table-primary'];
            }
        },
        'columns' => [
            ['class' => SerialColumn::class],

            'ticket.title',
            'ticket.body:ntext',
            'ticket.created_at:datetime',
            'ticket.updated_at:datetime',

            [
                'attribute' => 'View',
                'content' => function($model) {
                    return Html::a('View', ['view', 'id' => $model->id], ['class' => 'btn btn-primary']);
                }
            ],
        ],
    ]); ?>



</div>

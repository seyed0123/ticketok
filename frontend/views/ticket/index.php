<?php

use common\models\Ticket;
use common\models\UserTicket;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array<int, int> $ticketStatuses */

$this->title = 'Inbox Tickets';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ticket-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Ticket', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) use ($ticketStatuses) {

            if (isset($ticketStatuses[$model->id]) && $ticketStatuses[$model->id]['status'] === UserTicket::STATUS_SEEN) {
                return ['class' => 'table-secondary opacity-25'];
            } else {
                return ['class' => 'table-primary'];
            }
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'title',
            'body:ntext',
            'created_at:datetime',
            'updated_at:datetime',

            [
                'attribute' => 'View',
                'content' => function($model) {
                    return Html::a('View', ['view', 'id' => $model->id], ['class' => 'btn btn-primary']);
                }
            ],
        ],
    ]); ?>



</div>

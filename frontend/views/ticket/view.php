<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Ticket $model */
/** @var array<string, int> $ticketStatuses */
/** @var boolean $back */

$urlPath = [];
if($back){
    $urlPath = ['label' => 'Tickets', 'url' => ['sent']];
}else{
    $urlPath = ['label' => 'Inbox', 'url' => ['index' ]];
}
$this->title = $model->title;
Yii::warning($urlPath,'application');
$this->params['breadcrumbs'][] = $urlPath;
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ticket-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php if (Yii::$app->user->id === $model->author_id): ?>
        <p>
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    <?php endif; ?>
        <div class="container">
            <div class="row alert alert-dark ">
                <h1 class='text-center ' style="font-size: 36px; font-weight: bold; margin-bottom: 20px;">
                    <?= Html::encode($model->title) ?>
                </h1>
                <div class="row p-3">
                    <div class="text-left alert alert-light " style="margin-bottom: 20px;">
                        <?= nl2br(Html::encode($model->body)) ?>
                    </div>
                </div>
            </div>

            <div class="card my-2 fw-light" style="width: 18rem;">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-muted">Create At :  <?= Yii::$app->formatter->asDatetime($model->created_at, 'php:F j, Y, g:i a') ?></li>
                    <li class="list-group-item text-muted">Update At : <?= Yii::$app->formatter->asDatetime($model->updated_at, 'php:F j, Y, g:i a') ?></li>
                    <li class="list-group-item text-muted" >Status : <?= $model->getStatusLabels()[$model->status] ?></li>
                </ul>
            </div>

            <?php if (Yii::$app->user->id === $model->author_id): ?>
            <table class="table table-striped table-bordered bg-transparent ">
                <thead>
                <tr class="table-primary">
                    <th>
                        Username
                    </th>

                    <th>
                        Status
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($ticketStatuses as $ticketStatus){
                    echo "<tr>
                            <th>
                                " . htmlspecialchars($ticketStatus['username']) . "
                            </th>
                            <th>
                                " . htmlspecialchars($ticketStatus['status']) . "
                            </th>
                        </tr>";
                }?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

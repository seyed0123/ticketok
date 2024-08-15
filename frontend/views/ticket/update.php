<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Ticket $model */

$this->title = 'Update Ticket: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Tickets', 'url' => ['sent ']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="ticket-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::$app->name;
?>
<div class="site-index">
    <div class="p-5 mb-4 bg-transparent rounded-3 btn-outline-success">
        <div class="container-fluid py-5 text-center">
            <img src="<?= Url::to('@web/ticketok.jpg') ?>" alt="Ticketok Image" class="img-fluid rounded" style="max-width: 25%">
            <h1 class="display-4 my-3">Ticketok</h1>
            <p class="fs-5 fw-light">Your go-to platform for creating, managing, and utilizing tickets with ease.</p>
            <p><?= Html::a('Get started with Ticketok', Url::to('/ticket', true), ['class' => 'btn btn-lg btn-outline-info']) ?></p>
        </div>
    </div>

</div>

<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Ticket $model */
/** @var yii\bootstrap5\ActiveForm $form */
?>

<div class="ticket-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'body')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->radioList(['0' => 'Draft', '1' => 'Send']) ?>

    <?= $form->field($model, 'usernames')->textInput([
        'maxlength' => true,
        'placeholder' => 'Enter usernames, separated by commas',
    ])->label('Usernames') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

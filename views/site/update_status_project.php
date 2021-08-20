<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii \ helpers \ ArrayHelper;

?>
<div class = 'test col-xs-4' >
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
    <?= $form->field($model, 'txt')->textarea(); ?>

    <?= Html::submitButton('ОК',['class' => 'btn btn-success']) ?>
    <br>
    <br>

    <?php ActiveForm::end() ?>
</div>

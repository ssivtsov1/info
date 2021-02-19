<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii \ helpers \ ArrayHelper;
$arr = ['Выбор года','2018','2019','2020','2021'];
?>
<div class = 'test col-xs-3' >
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'id_project')->label('Проект') -> textInput() -> dropDownList (ArrayHelper::map(
        app\models\plan::findbysql('
       select id, txt from project')
            ->all(), 'id', 'txt'),
        ['prompt' => 'Выбор проекта',]) ?>

    <?= $form->field($model, 'id_status')->label('Статус плана')-> textInput() -> dropDownList (ArrayHelper::map(
        app\models\plan::findbysql('
       select id, status from status_plan')
            ->all(), 'id', 'status'),
        ['prompt' => 'Выбор статуса',]) ?>

<!--    --><?//= $form->field($model, 'year')->label('Год')-> textInput() -> dropDownList ($arr)?>
<!---->
<!--    --><?//= $form->field($model, 'month')->label('Месяц')  -> textInput() -> dropDownList (ArrayHelper::map(
//        app\models\plan::findbysql('
//       select id, month from month')
//            ->all(), 'id', 'month'),
//        ['prompt' => 'Выбор месяца',]) ?>

    <?= $form->field($model, 'txt')->label('План')  -> textInput() ?>

    <?= $form->field($model, 'speed')->label('Степень срочности')  -> textInput() -> dropDownList (ArrayHelper::map(
        app\models\plan::findbysql('
       select id, speed from vw_plans group by speed')
            ->all(), 'id', 'speed'),
        ['prompt' => 'Выбор степени срочности',]) ?>

    <?= Html::submitButton('Надіслати',['class' => 'btn btn-success']) ?>

    <?php ActiveForm::end() ?>
</div>

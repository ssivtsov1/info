<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii \ helpers \ ArrayHelper;
$arr = ['Выбор года','2018','2019','2020','2021'];
$arr1 = ['Степень срочности','1','2','3','4','5','6','7','8','9','10'];
?>

<div class = 'test col-xs-3' >
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'projects')->label('Проект') -> textInput() -> dropDownList (ArrayHelper::map(
        app\models\plan::findbysql('
       select id, txt from project')
            ->all(), 'id', 'txt'),
        ['prompt' => 'Выбор проекта',]) ?>

    <?= $form->field($model, 'plan_status')->label('Статус плана')-> textInput() -> dropDownList (ArrayHelper::map(
        app\models\plan::findbysql('
       select id, status from status_plan')
            ->all(), 'id', 'status'),
        ['prompt' => 'Выбор статуса',]) ?>

    <?= $form->field($model, 'year')->label('Год')-> textInput() -> dropDownList ($arr)?>

    <?= $form->field($model, 'month')->label('Месяц')  -> textInput() -> dropDownList (ArrayHelper::map(
        app\models\plan::findbysql('
       select id, month from month')
            ->all(), 'id', 'month'),
        ['prompt' => 'Выбор месяца',]) ?>

    <?= $form->field($model, 'txt')->label('План')  -> textarea() ?>
    <?= $form->field($model, 'speed')-> dropDownList ( $arr1 ) ?>

    <?= Html::submitButton('OK',['class' => 'btn btn-success']) ?>
    <br>
    <br>
    <?php ActiveForm::end() ?>
</div>

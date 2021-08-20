<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii \ helpers \ ArrayHelper;
$arr = ['Выбор года','2018','2019','2020','2021'];
$arr1 = ['Степень срочности','1','2','3','4','5','6','7','8','9','10'];
?>
<div class = 'test col-xs-4' >
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
    <?php
    echo $form->field($model, 'date')->
    widget(\yii\jui\DatePicker::classname(), [
    'language' => 'uk'
    ]); ?>
    <?= $form->field($model, 'txt')->label('Текст')  -> textarea(['rows' => 8, 'cols' => 35]) ?>

    <?= $form->field($model, 'id_project')->label('Проект') -> textInput() -> dropDownList (ArrayHelper::map(
        app\models\Plan1::findbysql('
       select id as id_project, txt from project')
            ->all(), 'id_project', 'txt'),
        ['prompt' => 'Выбор проекта',]) ?>

    <?= Html::submitButton('ОК',['class' => 'btn btn-success']) ?>
    <br>
    <br>

    <?php ActiveForm::end() ?>
</div>

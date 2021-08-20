<?php


use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\CheckboxColumn;
use yii\grid\SerialColumn;

//$this->title = 'Відключення у електромережах';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= Html::a('Добавить', ['createplan','sql'=>$sql], ['class' => 'btn btn-success']) ?>
<? echo ' ';?>
<?= Html::a('Поиск', ['plan_forma'], ['class' => 'btn btn-success']) ?>
<div class="site-spr1">

    <h3><?= Html::encode($this->title) ?></h3>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout'=>"{summary}\n{items}",
//        'filterModel' => $searchModel,
        'summary' => false,
        'emptyText' => 'Нічого не знайдено',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                /**
                 * Указываем класс колонки
                 */
                'class' => \yii\grid\ActionColumn::class,
                'buttons'=>[

                    'update'=>function ($url, $model) use ($sql) {
                        $customurl=Yii::$app->getUrlManager()->
                        createUrl(['/site/update_plan','id'=>$model['id'],'mod'=>'update_plan','sql'=>$sql]);
                        return \yii\helpers\Html::a( '<span class="glyphicon glyphicon-pencil"></span>', $customurl,
                            ['title' => Yii::t('yii', 'Редагувати'), 'data-pjax' => '0']);
                    },
                    'delete'=>function ($url, $model) use ($sql) {
                        $customurl=Yii::$app->getUrlManager()->
                        createUrl(['/site/delete_rec','id'=>$model['id'],'mod'=>'plan','sql'=>$sql]); //$model->id для AR
                        return \yii\helpers\Html::a( '<span class="glyphicon glyphicon-remove-circle"></span>', $customurl,
                            ['title' => Yii::t('yii', 'Видалити'),'data' => [
                                'confirm' => 'Ви впевнені, що хочете видалити цей запис ?',
                            ], 'data-pjax' => '0']);
                    },
                ],
                'template' => '{update} {delete}',
            ],
            'projects',
            'status',
            'plan_status',
            'year',
            'month',
            'txt',
            'speed',
        ],
    ]); ?>



</div>




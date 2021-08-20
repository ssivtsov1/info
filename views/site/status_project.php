<?php


use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\CheckboxColumn;
use yii\grid\SerialColumn;

$this->title = 'Довідник статусів проекта';
$this->params['breadcrumbs'][] = $this->title;
echo Html::a('Додати', ['createstatusproject'], ['class' => 'btn btn-success'])
?>
<div class="site-spr1">

    <h3><?= Html::encode($this->title) ?></h3>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'summary' => false,
        'emptyText' => 'Нічого не знайдено',
        'columns' => [
            [
                /**
                 * Указываем класс колонки
                 */
                'class' => \yii\grid\ActionColumn::class,
                'buttons'=>[

                    'update'=>function ($url, $model)  {
                        $customurl=Yii::$app->getUrlManager()->
                        createUrl(['/site/update_status_project','id'=>$model['id'],'mod'=>'update_project']);
                        return \yii\helpers\Html::a( '<span class="glyphicon glyphicon-pencil"></span>', $customurl,
                            ['title' => Yii::t('yii', 'Редагувати'), 'data-pjax' => '0']);
                    },
                    'delete'=>function ($url, $model)  {
                        $customurl=Yii::$app->getUrlManager()->
                        createUrl(['/site/delete_status_project','id'=>$model['id'],'mod'=>'delete_project']); //$model->id для AR
                        return \yii\helpers\Html::a( '<span class="glyphicon glyphicon-remove-circle"></span>', $customurl,
                            ['title' => Yii::t('yii', 'Видалити'),'data' => [
                                'confirm' => 'Ви впевнені, що хочете видалити цей запис ?',
                            ], 'data-pjax' => '0']);
                    },
                ],
                'template' => '{update} {delete}',
            ],
            'id',
            'txt',
        ],
    ]); ?>



</div>




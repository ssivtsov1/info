<?php


use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\CheckboxColumn;
use yii\grid\SerialColumn;

$this->title = 'Телефонний довідник';
$zag = 'Всього знайдено: '.$kol;
//$this->params['breadcrumbs'][] = $this->title;

?>
<?//= Html::a('Добавити', ['createtransp'], ['class' => 'btn btn-success']) ?>
<div class="site-spr">
    <h5><?= Html::encode($zag) ?></h5>
    <?php if(!isset(Yii::$app->user->identity->role)) { ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'layout'=>"{items}",
        'summary' => false,
        'emptyText' => 'Нічого не знайдено',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'fio',
            'post',
            ['attribute' =>'tel_mob',
                'value' => function ($model){
                    $q = trim($model->tel_mob);
//                    $pos = strpos($q,',');
//                    if($pos>0)
//                    {
//                        $q1 = substr($q,0,$pos);
//                        $q2 = substr($q,$pos);
//                        $q1 = only_digit1($q1);
//                        $q2 = only_digit1($q2);
//                        if(strlen($q)==9) $q = '0'.$q;
//                    }
                    $tels = explode(',',$q);
                    $s = '';
                    $i = 0;
                    foreach ($tels as $t) {
                        $i++;
                        $q = only_digit($t);
                        if (strlen($q) == 9) $q = '0' . $q;
                        $q = tel_normal($q);
                        if($i>1)
                            $s.=','.chr(13).$q;
                        else
                            $s=$q;

                    }
                    return $s;
                },
                'format' => 'raw'
            ],
            'tel',
            ['attribute' =>'tel_town',
                'value' => function ($model){
                    $q = trim($model->tel_town);
                    return tel_normal($q);
                },
                'format' => 'raw'
            ],
            'main_unit',
            'unit_1',
            'unit_2',
            'email',
            'email_group'
       ],
]); 
             }   ?>

<?php if(isset(Yii::$app->user->identity->role)) { ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'layout'=>"{items}",
        'summary' => false,
        'emptyText' => 'Нічого не знайдено',
        'columns' => [
            
             [
                /**
                 * Указываем класс колонки
                 */
                'class' => \yii\grid\ActionColumn::class,
                 'buttons'=>[
                  'delete'=>function ($url, $model) use ($sql) {
                        $customurl=Yii::$app->getUrlManager()->createUrl(['/site/delete',
                            'id'=>$model['id'],'mod'=>'viewphone','sql'=>$sql]); //$model->id для AR
                        return \yii\helpers\Html::a( '<span class="glyphicon glyphicon-remove-circle"></span>', $customurl,
                                                ['title' => Yii::t('yii', 'Видалити'),'data' => [
                                                'confirm' => 'Ви впевнені, що хочете видалити цей запис ?',
                                                ], 'data-pjax' => '0']);
                  },

                  'update'=>function ($url, $model) use ($sql) {
                        $customurl=Yii::$app->getUrlManager()->createUrl(['/site/update',
                            'id'=>$model['id'],'mod'=>'viewphone','sql'=>$sql]); //$model->id для AR
                        return \yii\helpers\Html::a( '<span class="glyphicon glyphicon-pencil"></span>', $customurl,
                                                ['title' => Yii::t('yii', 'Редагувати'), 'data-pjax' => '0']);
                  }
                ],
                /**
                 * Определяем набор кнопочек. По умолчанию {view} {update} {delete}
                 */
                'template' => '{update} {delete}',
            ],
            
            ['class' => 'yii\grid\SerialColumn'],
            'tab_nom',
            'fio',
            'post',
            ['attribute' =>'tel_mob',
                'value' => function ($model){
                    $q = trim($model->tel_mob);
//                    $pos = strpos($q,',');
//                    if($pos>0)
//                    {
//                        $q1 = substr($q,0,$pos);
//                        $q2 = substr($q,$pos);
//                        $q1 = only_digit1($q1);
//                        $q2 = only_digit1($q2);
//                        if(strlen($q)==9) $q = '0'.$q;
//                    }
                    $tels = explode(',',$q);
                    $s = '';
                    $i = 0;
                    foreach ($tels as $t) {
                        $i++;
                        $q = only_digit($t);
                        if (strlen($q) == 9) $q = '0' . $q;
                        $q = tel_normal($q);
                        if($i>1)
                            $s.=','.chr(13).$q;
                        else
                            $s=$q;

                    }
                    return $s;
                },
                'format' => 'raw'
            ],
            'nazv',
            'rate',
            'type_tel',            
            'tel',
                       
            ['attribute' =>'tel_town',
                'value' => function ($model){
                    $q = trim($model->tel_town);
                    return tel_normal($q);
                },
                'format' => 'raw'
            ],
            'line',
            'phone_type',
            'main_unit',
            'unit_1',
            'unit_2',
            'email',
            'email_group',

                        
            
       ],
]); 
 
}    ?>
</div>




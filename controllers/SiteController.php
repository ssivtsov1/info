<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Url;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use app\models\ContactForm;
use app\models\InputData;
use app\models\employees;
use app\models\viewphone;
use app\models\list_workers;
use app\models\kyivstar;
use app\models\hipatch;
use app\models\requestsearch;
use app\models\tofile;
use app\models\forExcel;
use app\models\info;
use app\models\User;
use app\models\loginform;
use kartik\mpdf\Pdf;
//use mpdf\mpdf;
use yii\web\UploadedFile;

class SiteController extends Controller
{  /**
 * 
 * @return type
 *
 */

    //public $defaultAction = 'index';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    //  Происходит при запуске сайта
    public function actionIndex()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect(['site/more']);
        }
        if(strpos(Yii::$app->request->url,'/cek')==0)
            return $this->redirect(['site/more']);
        $model = new loginform();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['site/more']);
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    //  Происходит после ввода пароля
    public function actionMore($sql='0')
    {
      
        if($sql=='0') {

            $model = new InputData();

            $last = 607;
            if ($model->load(Yii::$app->request->post())) {
                //$searchModel = new employees();
                // Создание поискового sql выражения
                $where = '';

                if (!empty($model->main_unit)) {
                    if ($model->main_unit == $last) {
                        $where .= ' ';
                    } else {
                        $data = employees::find()->select(['main_unit'])
                            ->where('id_name=:id_name', [':id_name' => $model->main_unit])->all();
                        $main_unit = $data[0]->main_unit;
                        $where .= ' and main_unit=' . "'" . $main_unit . "'";
                    }
                }
                if (!empty($model->unit_1)) {

                    if ($model->unit_1 == $last) $where .= ' ';
                    else {
                        $data = employees::find()->select(['unit_1'])
                            ->where('id=:id', [':id' => $model->unit_1])->all();
                        $unit_1 = $data[0]->unit_1;
                        if ($unit_1 != 'Відділ по роботі з юридичними споживачами електроенергії')
                            $where .= ' and unit_1=' . "'" . $unit_1 . "'";
                        else
                            $where .= ' and unit_1=' . "'" . $unit_1 . "'" . ' or tab_nom=1538';
                    }

                }
                if (!empty($model->unit_2)) {
                    if ($model->unit_2 == $last) $where .= ' ';
                    else {
                        $data = employees::find()->select(['unit_2'])
                            ->where('id=:id', [':id' => $model->unit_2])->all();
                        $unit_2 = $data[0]->unit_2;
                        $where .= ' and unit_2=' . "'" . $unit_2 . "'";
                    }
                }
                if (!empty($model->fio)) {
                    $where .= ' and fio like ' . "'%" . $model->fio . "%'";
                }
                if (!empty($model->tel_mob)) {
                    $tel_mob = trim($model->tel_mob);
                    if (substr($tel_mob, 0, 1) == '0') $tel_mob = substr($tel_mob, 1);
                    $tel_mob = only_digit($tel_mob);
                    $where .= ' and tel_mob like ' . "'%" . $tel_mob . "%'";
                }
                if (!empty($model->tel_town)) {
                    $tel_town = trim($model->tel_town);
                    $tel_town = only_digit($tel_town);
                    $where .= ' and tel_town like ' . "'%" . $tel_town . "%'";
                }
                if (!empty($model->tel)) {
                    $where .= ' and tel like ' . "'%" . $model->tel . "%'";
                }
                if (!empty($model->post)) {
                    $where .= ' and post like ' . "'%" . $model->post . "%'";
                }
                $where = trim($where);
                if (empty($where)) $where = '';
                else
                    $where = ' where ' . substr($where, 4);


                $sql = "select *,rate_person(post) as sort1 from vw_phone " . $where . ' order by sort1,fio';

//            debug($sql);
//            return;

                $data = viewphone::findBySql($sql)->all();
//            $dataProvider = new ActiveDataProvider([
//                'query' => viewphone::findBySql($sql),
//               // 'sort' => ['defaultOrder'=> ['sort'=>SORT_ASC,'unit_2'=>SORT_ASC]]
//            ]);
                $kol = count($data);
                $searchModel = new viewphone();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $sql);


//            $dataProvider->sort->attributes['sort'] = [
//            'asc' => ['sort' => SORT_ASC,'unit_2'=>SORT_ASC],
//            'desc' => ['sort' => SORT_DESC,'unit_2' => SORT_DESC],
//            ];

                                  
                $session = Yii::$app->session;
                $session->open();
                $session->set('view', 1);
                //'data' => $data

                return $this->render('viewphone', [
                    'dataProvider' => $dataProvider, 'searchModel' => $searchModel, 'kol' => $kol, 'sql' => $sql]);
            } else {
                return $this->render('inputdata', [
                    'model' => $model,
                ]);
            }
        }
        else{
             // Если передается параметр $sql
            $data = viewphone::findBySql($sql)->all();
            $searchModel = new viewphone();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $sql);
            $kol = count($data);

            $session = Yii::$app->session;
            $session->open();
            $session->set('view', 1);

            return $this->render('viewphone', ['data' => $data,
                'dataProvider' => $dataProvider, 'searchModel' => $searchModel, 'kol' => $kol, 'sql' => $sql]);
        }
    }

    // *** Просмотр телефонов служащих 
    // срабатывает при входе в пункт меню "Працівники"   
    public function actionEmployees()
    {
        $searchModel = new employees();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $session = Yii::$app->session;
        $session->open();
        $session->set('view', 1);
            
        return $this->render('employees', [
            'model' => $searchModel,'dataProvider' => $dataProvider,'searchModel' => $searchModel,
        ]);
    }         
    
    //    Удаление записей из справочника
    public function actionDelete($id,$mod,$sql)
    {   // $id  id записи
        // $mod - название модели
        // $sql- sql запрос, с помощью которого были извлечены данные перед удалением
        if($mod=='viewphone')
        {
        $model = list_workers::findOne($id);
        $tab_nom = $model->tab_nom;
        $model->delete();  // Удаление записи из списка рабочих
        $data_mob = kyivstar::find()->select('id')
                ->where('tab_nom=:tab_nom', [':tab_nom' => $tab_nom])->one();
        if(!empty($data_mob->id))
            $data_mob->delete(); // Удаление записи из списка мобильных телефонов
        $data = hipatch::find()->select('id')
                ->where('tab_nom=:tab_nom', [':tab_nom' => $tab_nom])->one();
        if(!empty($data->id))
             $data->delete();  // Удаление записи из списка внутренних и городских телефонов
        }
        return $this->redirect(['site/more','sql' => $sql]);
    }
    
    
     //    Удаление записей из справочника (Працівники)
    public function actionDelete_emp($id,$mod)
    {   // $id  id записи
        // $mod - название модели
        if($mod=='viewphone')
        {
        $model = list_workers::findOne($id);
        $tab_nom = $model->tab_nom;
        $model->delete();  // Удаление записи из списка рабочих
        $data_mob = kyivstar::find()->select('id')
                ->where('tab_nom=:tab_nom', [':tab_nom' => $tab_nom])->one();
        if(!empty($data_mob->id))
            $data_mob->delete(); // Удаление записи из списка мобильных телефонов
        $data = hipatch::find()->select('id')
                ->where('tab_nom=:tab_nom', [':tab_nom' => $tab_nom])->one();
        if(!empty($data->id))
             $data->delete();     // Удаление записи из списка внутренних и городских телефонов
        }
        return $this->redirect(['site/employees']);
    }
    
    
    //    Обновление записей из справочника
    public function actionUpdate($id,$mod,$sql)
    {
        // $id  id записи
        // $mod - название модели
        // $sql- sql запрос, с помощью которого были извлечены данные перед обновлением
        if($mod=='viewphone')
              $model = viewphone::find()
                ->where('id=:id', [':id' => $id])->one();
        
        $tab_nom = $model->tab_nom;
        if ($model->load(Yii::$app->request->post()))
        {  
            $q = trim($model->tel_mob);
            $q = only_digit1($q);
            if(substr($q,0,1)=='0')
                    $q=substr($q,1);
            
            $t = trim($model->tel_town);
            $t = only_digit1($t);
            
            // Изменение Ф.И.О, должности
            $data = list_workers::findOne($id);
            $data->post = $model->post;
            $data->fio = $model->fio;
            $data->save();
            
            $data_mob = kyivstar::find()
                ->where('tab_nom=:tab_nom', [':tab_nom' => $tab_nom])->one();
            
            if(!empty($data_mob->id)){
                // Изменение мобильного телефона
                $data_mob->tel = $q;
                $data_mob->rate = $model->rate;
                $data_mob->type_tel = $model->type_tel;
                $data_mob->save();
            }
            else
            {   // Добавление мобильного телефона
                $data_mob = new kyivstar();
                $data_mob->tab_nom = $tab_nom;
                $data_mob->fio = $model->fio;
                $data_mob->tel = $q;
                $data_mob->rate = $model->rate;
                $data_mob->type_tel = $model->type_tel;
                $data_mob->save();
            }
            $data_town = hipatch::find()
                ->where('tab_nom=:tab_nom', [':tab_nom' => $tab_nom])->one();
            
            if(!empty($data_town->id)){
                // Изменение городского, внутреннего телефона
                $data_town->tel = $model->tel;
                $data_town->tel_town = $t;
                $data_town->phone_type = $model->phone_type;
                $data_town->line = $model->line;
                $data_town->save();
                
            }
            else{
                // Добавление городского, внутреннего телефона
                $data_town = new hipatch();
                $data_town->tab_nom = $tab_nom;
                $data_town->fio = $model->fio;
                $data_town->tel = $model->tel;
                $data_town->tel_town = $t;
                $data_town->phone_type = $model->phone_type;
                $data_town->line = $model->line;
                $data_town->save();
            }

            if($mod=='viewphone')
                $this->redirect(['site/more','sql' => $sql]);
            
        } else {
            if($mod=='viewphone')
            return $this->render('update_phone', [
                'model' => $model,
            ]);
        }
    }
    
    //    Обновление записей из справочника (Працівники)
    public function actionUpdate_emp($id,$mod)
    {
        // $id  id записи
        // $mod - название модели
        if($mod=='viewphone')
              $model = viewphone::find()
                ->where('id=:id', [':id' => $id])->one();
        
        $tab_nom = $model->tab_nom;
        if ($model->load(Yii::$app->request->post()))
        {  
            $q = trim($model->tel_mob);
            $q = only_digit1($q);
            if(substr($q,0,1)=='0')
                    $q=substr($q,1);
            
            $t = trim($model->tel_town);
            $t = only_digit1($t);
            
            // Изменение Ф.И.О, должности
            $data = list_workers::findOne($id);
            $data->post = $model->post;
            $data->fio = $model->fio;
            $data->save();
            
            $data_mob = kyivstar::find()
                ->where('tab_nom=:tab_nom', [':tab_nom' => $tab_nom])->one();
            
            if(!empty($data_mob->id)){
                // Изменение мобильного телефона
                $data_mob->tel = $q;
                $data_mob->rate = $model->rate;
                $data_mob->type_tel = $model->type_tel;
                $data_mob->save();
            }
            else
            {   // Добавление мобильного телефона
                $data_mob = new kyivstar();
                $data_mob->tab_nom = $tab_nom;
                $data_mob->fio = $model->fio;
                $data_mob->tel = $q;
                $data_mob->rate = $model->rate;
                $data_mob->type_tel = $model->type_tel;
                $data_mob->save();
            }
            $data_town = hipatch::find()
                ->where('tab_nom=:tab_nom', [':tab_nom' => $tab_nom])->one();
            
            if(!empty($data_town->id)){
                // Изменение городского, внутреннего телефона
                $data_town->tel = $model->tel;
                $data_town->tel_town = $t;
                $data_town->phone_type = $model->phone_type;
                $data_town->line = $model->line;
                $data_town->save();
                
            }
            else{
                // Добавление городского, внутреннего телефона
                $data_town = new hipatch();
                $data_town->tab_nom = $tab_nom;
                $data_town->fio = $model->fio;
                $data_town->tel = $model->tel;
                $data_town->tel_town = $t;
                $data_town->phone_type = $model->phone_type;
                $data_town->line = $model->line;
                $data_town->save();
            }

            if($mod=='viewphone')
                $this->redirect(['site/employees']);
            
        } else {
            if($mod=='viewphone')
            return $this->render('update_phone', [
                'model' => $model,
            ]);
        }
    }
    
    // Подгрузка подразделений, подчиненных главному - происходит при выборе главного подразделения
    public function actionGetunit_1($id_name) {
    Yii::$app->response->format = Response::FORMAT_JSON;
    if (Yii::$app->request->isAjax) {
        $data = employees::find()->select(['main_unit'])->where('id_name=:id',[':id' => $id_name])->all();
        $main_unit = $data[0]->main_unit;

       // if($main_unit<>"Підрозділи підпорядковані Генеральному директору")
        $sql = "select 607 as nomer,'607  Всі підрозділи' as unit_1
                union
                select cast(min(id) as char(3)) as nomer,concat(cast(min(id) as char(3)),'  ',unit_1) as unit_1 
                from vw_phone where main_unit="."'".$main_unit."'".
           // ' and unit_1<>"Апарат управління"'.
                ' group by unit_1';
//        else
//            $sql = "select cast(min(id) as char(3)) as nomer,concat(cast(min(id) as char(3)),'  ',unit_1) as unit_1
//            from vw_phone where main_unit="."'".$main_unit."'".
//                ' group by unit_1';
        if($id_name==607)
            $sql = "select 607 as nomer,'607  Всі підрозділи' as unit_1
                union select cast(min(id) as char(3)) as nomer,concat(cast(min(id) as char(3)),'  ',unit_1) as unit_1 
             from vw_phone where LENGTH(ltrim(rtrim(unit_1)))<>0 group by unit_1";

        $unit = employees::findBySql($sql)->all();
        return ['success' => true, 'unit' => $unit,'main_unit' => $main_unit];
    }
    return ['oh no' => 'you are not allowed :('];
    }

    // Подгрузка подразделений нижнего уровня
    public function actionGetunit($id,$main_unit) {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $data = employees::find()->select(['unit_1'])->where('id=:id',[':id' => $id])->all();
            $unit_1 = $data[0]->unit_1;

         $sql = "select 607 as nomer,'607  Всі підрозділи' as unit_2,0 as sort,'607  Всі підрозділи' as unit_3 
                union
             select cast(min(id) as char(3)) as nomer,concat(cast(min(id) as char(3)),'  ',unit_2) as unit_2,
          if(unit_2='Керівництво',0,1) as sort,unit_2 as unit3
        from vw_phone where unit_1="."'".$unit_1."'".
             ' and main_unit='."'".$main_unit."'".
             ' group by unit_2 order by 3,4';

            if($id==607)
                $sql = "select 607 as nomer,'607  Всі підрозділи' as unit_2,0 as sort,'607  Всі підрозділи' as unit_3 
                union
                Select cast(min(id) as char(3)) as nomer,concat(cast(min(id) as char(3)),'  ',unit_2) as unit_2,
                 if(unit_2='Керівництво',0,1) as sort,unit_2 as unit3
                from vw_phone where LENGTH(ltrim(rtrim(unit_2)))<>0
                 group by unit_2 order by 3,4";

            $data = employees::findBySql($sql)->all();
            return ['success' => true, 'data' => $data];
        }
        return ['oh no' => 'you are not allowed :('];
    }

    //    Страница о программе
    public function actionAbout()
    {
        $model = new info();
        $model->title = 'Про сайт';
        $model->info1 = "За допомогою цього сайту здійснюється пошук телефонних номерів працівників Центральної Енергетичної Компанії, відповідно заданим параметрам пошуку.";
        $model->style1 = "d15";
        $model->style2 = "info-text";
        $model->style_title = "d9";

        return $this->render('about', [
            'model' => $model]);
    }

    //    Сброс в Excel результатов рассчета
    public function actionExcel($kind,$nazv,$rabota,$delivery,$transp,$all,$nds,$all_nds)
    {
        $k1='Результат розрахунку для послуги: '.$nazv;
        $param = 0;
        $model = new forExcel();
        $model->nazv = $nazv;
        $model->rabota = $rabota;
        $model->delivery = $delivery;
        $model->transp = $transp;
        $model->all = $all;
        $model->nds = $nds;
        $model->all_nds = $all_nds;
        if ($kind == 1) {
            \moonland\phpexcel\Excel::widget([
                'models' => $model,
                'mode' => 'export', //default value as 'export'
                'format' => 'Excel2007',
                'hap' => $k1, //cтрока шапки таблицы'hap' => $k1,
                'data_model' => $param,
                'columns' => ['nazv', 'rabota', 'delivery', 'transp', 'all', 'nds', 'all_nds',
                ],
            ]);
        }
        if ($kind == 2) {
            \moonland\phpexcel\Excel::widget([
                'models' => $model,
                'mode' => 'export', //default value as 'export'
                'format' => 'Excel2007',
                'hap' => $k1, //cтрока шапки таблицы'hap' => $k1,
                'data_model' => $param,
                'columns' => ['nazv', 'all', 'nds', 'all_nds',
                ],
            ]);
        }
        return;
    }

// Добавление новых пользователей
    public function actionAddAdmin() {
        $model = User::find()->where(['username' => 'buh1'])->one();
        if (empty($model)) {
            $user = new User();
            $user->username = 'buh1';
            $user->email = 'buh1@ukr.net';
            $user->setPassword('afynfpbz');
            $user->generateAuthKey();
            if ($user->save()) {
                echo 'good';
            }
        }
    }

// Выход пользователя
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}

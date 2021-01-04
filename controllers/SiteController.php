<?php

namespace app\controllers;
//namespace app\models;

use app\models\A_diary;
use app\models\A_diary_search;
use app\models\phones_sap;
use app\models\phones_sap_search;
use app\models\Plan;
use app\models\plan_forma;
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
use app\models\cdata;
use app\models\employees;
use app\models\shtrafbat;
use app\models\viewphone;
use app\models\list_workers;
use app\models\kyivstar;
use app\models\hipatch;
use app\models\tel_vi;
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

    public $curpage;

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
        $this->curpage=1;
        if($sql=='0') {

            $model = new InputData();
            $flag_fio = 0;
            //$last = 630;
            $last = 1630;
            $cdata = cdata::find()->all();
            $date_b=$cdata[0]['date_b'];
            $date_e=$cdata[0]['date_e'];
            $gendir=$cdata[0]['gendir_const'];

            $date=date('Y-m-d');
            if($date>=$date_b && $date<=$date_e) $gendir=$cdata[0]['gendir'];

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

//                    debug($model->unit_1);
//                    return;

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
                    $flag_fio = 1;
                    $where .= ' and (fio like ' . '"%' . $model->fio . '%"' .' or fio_ru like ' . '"%' . $model->fio . '%")';
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
                    switch($model->tel){
                        case '*':
                            $where .= ' and tel is not null';
                            break;
                        case '?':
                            $where .= ' and tel is null';
                            break;
                        default:
                            $where .= ' and tel like ' . "'%" . $model->tel . "%'";
                            break;
                    }
                }
                if (!empty($model->post)) {
                    $where .= ' and post like ' . "'%" . $model->post . "%'";
                }

                if (!empty($model->sex)) {
                    if($model->sex==1)
                        $where .= ' and extract_name(fio) in (select name from man_name where sex=0)';
                    if($model->sex==2)
                        $where .= ' and extract_name(fio) in (select name from man_name where sex=1)';
                }

                $where = trim($where);
                if (empty($where)) $where = '';
                else
                    $where = ' where ' . substr($where, 4);


                $sql = "select *,rate_person(post) as sort1,rate_group(unit_2) as sort2 from vw_phone " . $where . ' order by sort1,sort2,fio';

//            debug($sql);
//            return;

                $f=fopen('aaa','w+');
                fputs($f,$sql);

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
                // Ищем похожие фамилии, если не найдена запись с введенной фамилией
                // по алгоритму Левенштейна
                $closest[0] = '';
                $closest[10] = '';  // Признак о нажатии кнопки отдела
                if($kol==0 && $flag_fio == 1) {
                    $shortest = -1;
                    $sql_l = "select distinct(first_word(fio)) as fio from vw_phone";
                    $data_l = viewphone::findBySql($sql_l)->all();
                    $j=0;
                    foreach ($data_l as $v) {
                        $vf = $v->fio;
                        // вычисляем расстояние между входным словом и текущим
                        $lev = levenshtein($model->fio, $vf);

                        // проверяем полное совпадение
                        if ($lev == 0) {

                            // это ближайшее слово (точное совпадение)
                            $closest[$j] = $vf;
                            $shortest = 0;

                            // выходим из цикла - мы нашли точное совпадение
                            break;
                        }

                        // если это расстояние меньше следующего наименьшего расстояния
                        // ИЛИ если следующее самое короткое слово еще не было найдено
                        if ($lev <= $shortest || $shortest < 0) {
                            // устанивливаем ближайшее совпадение и кратчайшее расстояние
                            if($lev<3){
                                $closest[$j]  = $vf;
                                $shortest = $lev;
                                $j++;
                            }
                        }
                        
                    }
                    
                    }
                    
                 
                
                $session = Yii::$app->session;
                $session->open();
                $session->set('view', 1);
                //'data' => $data

                return $this->render('viewphone', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel, 'kol' => $kol,'sql' => $sql,'closest' => $closest]);
            } else {

                return $this->render('inputdata', [
                    'model' => $model,'gendir' => $gendir
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

//Щеденники

    public function actionA_diary_forma()
    {
        $model = new A_diary();
        $searchModel = new A_diary_search();
//    $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $sql);
//        $model = $model::find()->all();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
//        debug('1111111111111');
            $sql = "SELECT date,txt,projects,status
FROM vw_diary 
where 1=1";
//            if (!empty($model->txt)) {
//                $sql2 = '(select txt from plan where id ='. $model->txt.')';
//                $model->txt = $sql2;
//                $sql = $sql . ' and txt =' . $model->txt  ;
//            }
//        debug($sql);
//        return;
            if (!empty($model->projects)) {
                $sql = $sql . ' and id_project =' . "'" . $model->projects . "'";
            }
//        debug($sql);
//        return;
            if (!empty($model->status)) {
                $sql = $sql . ' and id_status =' . "'" . $model->status . "'";
            }
//                debug($sql);
//        return;
//            if (!empty($model->year)) {
//                if ($model->year == '1')
//                    $model->year = '2018';
//                if ($model->year == '2')
//                    $model->year = '2019';
//                $sql = $sql . ' and year =' . "'" . $model->year . "'";
//            }
////                        debug($sql);
////        return;
            $sql = $sql . ' ORDER BY 3';
//            debug($sql);
//            return;
//            $data = Off_site::findbysql($sql)->asArray()
//                ->all();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $sql);
//            debug($sql);
//            return;
            $dataProvider->pagination = false;
            return $this->render('a_diary_forma_2', [
                'model' => $searchModel,'dataProvider' => $dataProvider,'searchModel' => $searchModel,
            ]);
        } else {
            return $this->render('a_diary_forma', compact('model'));
        }
    }


    public function actionPlan_forma()
    {
        $model = new Plan_forma();
        $searchModel = new Plan();
//    $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $sql);
//        $model = $model::find()->all();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
//        debug('1111111111111');
            $sql = "SELECT projects, plan_status, year, month, txt, speed
            FROM vw_plans 
            where 1=1";

            if (!empty($model->projects)) {
                $sql = $sql . ' and id_project =' . "'" . $model->projects . "'";
            }
//        debug($sql);
//        return;
            if (!empty($model->plan_status)) {
                $sql = $sql . ' and id_status =' . "'" . $model->plan_status . "'";
            }
//                debug($sql);
//        return;
            if (!empty($model->year)) {
                if ($model->year == '1')
                    $model->year = '2018';
                if ($model->year == '2')
                    $model->year = '2019';
                $sql = $sql . ' and year =' . "'" . $model->year . "'";
            }
//                        debug($sql);
//        return;
            if (!empty($model->month)) {
                $sql = $sql . ' and id_month =' . "'" . $model->month . "'";
            }
            if (!empty($model->txt)) {
                $sql2 = '(select txt from plan where id ='. $model->txt.')';
                $model->txt = $sql2;
                $sql = $sql . ' and txt =' . $model->txt  ;
            }
//        debug($sql);
//        return;
            if (!empty($model->speed)) {
                $sql2 = '(select speed from plan where id ='. $model->speed.')';
                $model->speed = $sql2;
                $sql = $sql . ' and speed =' . $model->speed;
            }
//        debug($sql);
//        return;
            $sql = $sql . ' ORDER BY 1';
//            debug($model);
//            return;
//            $data = Off_site::findbysql($sql)->asArray()
//                ->all();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $sql);
//            debug($sql);
//            return;
            $dataProvider->pagination = false;
            return $this->render('plan_forma_2', [
                'model' => $searchModel,'dataProvider' => $dataProvider,'searchModel' => $searchModel,
            ]);
        } else {
            return $this->render('plan_forma', compact('model'));

        }
    }




    public function actionPhones_sap()
    {
        $model = new phones_sap();
        $searchModel = new phones_sap_search();
//    $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $sql);
//        $model = $model::find()->all();
//        Yii::$app->response->format = Response::FORMAT_JSON;
//        $c = mb_substr($fio,0,1,"UTF-8");
//        $code = ord($c);
//        if($code<128) $fio=recode_c(strtolower($fio));
//
//        $name1 = trim(mb_strtolower($fio,"UTF-8"));
//        $name2 = trim(mb_strtoupper($fio,"UTF-8"));
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
//        debug('1111111111111');
            $sql = "SELECT *
FROM contacty_sap
where 1=1";
            if (!empty($model-> fio)) {
                $sql = $sql . " and fio like '" .$model->fio ."%'";
            }
//                debug($sql);
//        return;
            if (!empty($model-> company)) {
                if ($model->company == '1')
                    $model->company = '"Виконавець"';
                if ($model->company == '2')
                    $model->company = '"ВОЕ"';
                if ($model->company == '3')
                    $model->company = '"СОЕ"';
                if ($model->company == '4')
                    $model->company = '"ЦЕК"';
                if ($model->company == '5')
                    $model->company = '"ЧОЕ"';
                if ($model->company == '6')
                    $model->company = '"ЧОЕ (викл.?)"';
                $sql = $sql . " and company = " .$model->company;
            }
//                debug($sql);
//        return;
            $sql = $sql . ' ORDER BY 1';
//            debug($model);
//            return;
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $sql);
//            debug($sql);
//            return;
            $dataProvider->pagination = false;
            return $this->render('phones_sap_2', [
                'model' => $searchModel,'dataProvider' => $dataProvider,'searchModel' => $searchModel,
            ]);
        } else {
            return $this->render('phones_sap', compact('model'));
        }
    }


}

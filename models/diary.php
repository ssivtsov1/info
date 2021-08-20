<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;

class Diary extends \yii\db\ActiveRecord
{
    public $projects;
    public $status;

    public static function tableName()
    {
        return 'diary';
    }

    public function attributeLabels()
    {
        return [
            'id' => '',
            'date' => 'Дата записи',
            'txt' => 'Текст записи',
            'projects' => 'Название проекта',
            'status' => 'Название статуса проекта',
        ];
    }

    public static function getDb()
    {
        return Yii::$app->get('db_mysql');
    }

    public function rules()
    {
        return [

            [['id','date','txt', 'projects', 'status','id_project',
            ], 'safe'],
            [['date','txt', 'id_project'
            ], 'required'],
        ];
    }

    public function search($params, $sql)
    {
        $query = diary::findBySql($sql);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'txt', $this->txt]);
        return $dataProvider;
    }

}
<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;

class Status_project extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'status_project';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'txt' => 'Статус проекта',
        ];
    }


    public function rules()
    {
        return [

            [['id','txt'
            ], 'safe'],

        ];
    }

    public function search($params)
    {
        $query = Status_project::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        return $dataProvider;
    }

    public static function getDb()
    {
        return Yii::$app->get('db_mysql');
    }


    public function getId()
    {
        return $this->getPrimaryKey();
    }

}
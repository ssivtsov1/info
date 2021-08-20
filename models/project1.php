<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;

class Project1 extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'project';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'txt' => 'Проект',
        ];
    }

    public static function getDb()
    {
        return Yii::$app->get('db_mysql');
    }

    public function rules()
    {
        return [
            [['id','id_status','txt'], 'safe'],
        ];
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

}
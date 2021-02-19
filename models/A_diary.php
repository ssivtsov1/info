<?php

namespace app\models;

use yii\base\Model;




class A_diary extends Model
{
    public $date;
    public $txt;
    public $projects;
    public $status;


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
    public function rules()
    {
        return [

            [['id','date','txt', 'projects', 'status','id_status',
            ], 'safe'],

        ];
    }

}
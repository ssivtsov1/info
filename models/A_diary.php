<?php

namespace app\models;

use yii\base\Model;




class A_diary extends Model
{
    public $date1;
    public $date2;
    public $txt;
    public $projects;
    public $status;


    public function attributeLabels()
    {
        return [
            'id' => '',
            'date1' => 'Дата записи с' ,
            'date2' => 'Дата записи по' ,
            'txt' => 'Текст записи',
            'projects' => 'Название проекта',
            'status' => 'Название статуса проекта',
        ];
    }
    public function rules()
    {
        return [

            [['id','date1','date2','txt', 'projects', 'status','id_status',
            ], 'safe'],
//            [['date','txt', 'projects'
//            ], 'required'],

        ];
    }

}
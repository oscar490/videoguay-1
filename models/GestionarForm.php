<?php

namespace app\models;

use yii\base\Model;

class GestionarForm extends Model
{
    /**
     * El ńumero del socio.
     * @var string
     */
    public $numero;

    /**
     * El código de la película.
     * @var string
     */
    public $codigo;

    public function formName()
    {
        return '';
    }

    public function attributeLabels()
    {
        return [
            'numero' => 'Número de socio',
            'codigo' => 'Código de la película',
        ];
    }

    public function rules()
    {
        return [
            [['numero'], 'required'],
            [['numero', 'codigo'], 'number'],
            [
                ['numero'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Socios::className(),
                'targetAttribute' => ['numero' => 'numero'], ],
            [
                ['codigo'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Peliculas::className(),
                'targetAttribute' => ['codigo' => 'codigo'],
            ],
        ];
    }
}

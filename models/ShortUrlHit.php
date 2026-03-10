<?php

namespace app\models;

use yii\db\ActiveRecord;

class ShortUrlHit extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%short_url_hit}}';
    }

    public function rules()
    {
        return [
            [['short_url_id', 'ip'], 'required'],
            [['short_url_id'], 'integer'],
            [['ip'], 'string', 'max' => 45],
        ];
    }
}


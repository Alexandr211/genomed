<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class ShortUrl extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%short_url}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['original_url'], 'required'],
            [['original_url'], 'string'],
            [['hit_count'], 'integer'],
            [['code'], 'string', 'max' => 16],
            [['code'], 'unique'],
        ];
    }

    public function getHits()
    {
        return $this->hasMany(ShortUrlHit::class, ['short_url_id' => 'id']);
    }

    public static function generateUniqueCode($length = 6): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $maxIndex = strlen($characters) - 1;

        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[random_int(0, $maxIndex)];
            }
        } while (static::find()->where(['code' => $code])->exists());

        return $code;
    }
}


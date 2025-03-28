<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use app\models\LinkLog;

/**
 * @property int $id
 * @property string $original_url
 * @property string $short_code
 * @property string $created_at
 * @property int $hit_counter
 */
class Link extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'link';
    }

    // Связь с логами переходов
    public function getLogs(): ActiveQuery
    {
        return $this->hasMany(LinkLog::class, ['link_id' => 'id']);
    }
}
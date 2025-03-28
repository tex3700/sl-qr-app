<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * @property int $id
 * @property int $link_id
 * @property string $ip_address
 * @property string $accessed_at
 */
class LinkLog extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'link_log';
    }

    public function getLink(): ActiveQuery
    {
        return $this->hasOne(Link::class, ['id' => 'link_id']);
    }
}
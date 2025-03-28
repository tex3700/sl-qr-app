<?php

namespace app\models;

use Yii;
use yii\base\Model;

class LinkForm extends Model
{
    public $url;

    public function rules(): array
    {
        return [
            [['url'], 'required'],
            [['url'], 'url', 'validSchemes' => ['http', 'https']],
        ];
    }

    public function checkUrlAvailability()
    {
        $ch = curl_init($this->url);

        curl_setopt_array($ch, [
            CURLOPT_NOBODY => true,          // Используем HEAD-запрос
            CURLOPT_FOLLOWLOCATION => true,  // Следовать редиректам
            CURLOPT_MAXREDIRS => 3,          // Максимум 3 редиректа
            CURLOPT_TIMEOUT => 5,            // Таймаут 5 секунд
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; YiiLinkValidator/1.0)', // Имитируем браузер
            CURLOPT_SSL_VERIFYPEER => false, // Отключить проверку SSL (для тестов)
            CURLOPT_RETURNTRANSFER => true   // Не выводить результат в поток
        ]);

        curl_exec($ch);

        // Проверка ошибок
        if(curl_errno($ch)) {
            Yii::error("cURL Error: " . curl_error($ch));
            curl_close($ch);
            return false;
        }

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $status >= 200 && $status < 400;
    }
}
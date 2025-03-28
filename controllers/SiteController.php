<?php

namespace app\controllers;

use app\models\{Link, LinkForm, LinkLog};
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Yii;
use yii\base\Exception;
use yii\filters\{AccessControl, VerbFilter};
use yii\helpers\Url;
use yii\web\{Controller, NotFoundHttpException, Response};

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['login', 'logout', 'signup'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login', 'signup'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['logout'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     */
    public function actionIndex(): string
    {
        $model = new LinkForm();
        return $this->render('index', ['model' => $model]);
    }

    /**
     * @throws Exception
     */
    public function actionGenerate(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new LinkForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // Проверка доступности URL
            if (!$model->checkUrlAvailability()) {
                return ['error' => 'URL недоступен'];
            }

            // Сохранение ссылки
            $link = new Link();
            $link->original_url = $model->url;
            $link->short_code = Yii::$app->security->generateRandomString(6);
            $link->created_at = date('Y-m-d H:i:s');
            $link->save();

            // Генерация QR
            $qrPath = $this->generateQrCode($link->short_code);

            return [
                'shortUrl' => Url::to(['/site/redirect', 'code' => $link->short_code], true),
                'qrCode' => $qrPath,
            ];
        }

        return ['error' => 'Невалидный URL'];
    }

    private function generateQrCode($code)
    {
        $writer = new PngWriter();
        $qrCode = new QrCode(
            data: '/site/redirect?code=' . $code,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        $result = $writer->write($qrCode);
        $qrDir = Yii::getAlias('@webroot/qr/');
        if (!file_exists($qrDir)) {
            mkdir($qrDir, 0755, true);
        }
        $filename = Yii::getAlias('@webroot/qr/') . $code . '.png';
        $result->saveToFile($filename);

        return Url::to('@web/qr/' . $code . '.png');
    }

    /**
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionRedirect($code)
    {
        $link = Link::findOne(['short_code' => $code]);
        if ($link) {
            // Логирование
            $log = new LinkLog();
            $log->link_id = $link->id;
            $log->ip_address = Yii::$app->request->userIP;
            $log->accessed_at = date('Y-m-d H:i:s');
            if (!$log->save()) {
                Yii::error("Ошибка сохранения лога: " . print_r($log->errors, true));
            }

            // Обновление счетчика
            $link->updateCounters(['hit_counter' => 1]);

            return $this->redirect($link->original_url);
        }
        throw new NotFoundHttpException('Ссылка не найдена');
    }

}

<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\ShortUrl;
use app\models\ShortUrlHit;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Writer\PngWriter;

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
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
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
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionShorten()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $url = Yii::$app->request->post('url');

        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return ['success' => false, 'error' => 'Укажите корректный URL (http/https).'];
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);
        if (!in_array($scheme, ['http', 'https'], true)) {
            return ['success' => false, 'error' => 'Поддерживаются только ссылки с http или https.'];
        }

        if (!$this->isUrlReachable($url)) {
            return ['success' => false, 'error' => 'Данный URL не доступен.'];
        }

        $model = new ShortUrl();
        $model->original_url = $url;
        $model->code = ShortUrl::generateUniqueCode();

        if (!$model->save()) {
            return ['success' => false, 'error' => 'Не удалось сохранить ссылку.'];
        }

        $shortUrl = Yii::$app->urlManager->createAbsoluteUrl(['redirect/go', 'code' => $model->code]);
        $qrUrl = Yii::$app->urlManager->createUrl(['site/qr', 'code' => $model->code]);

        return [
            'success' => true,
            'shortUrl' => $shortUrl,
            'qrUrl' => $qrUrl,
        ];
    }

    public function actionQr($code)
    {
        $model = ShortUrl::findOne(['code' => $code]);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('Ссылка не найдена.');
        }

        $redirectUrl = Yii::$app->urlManager->createAbsoluteUrl(['redirect/go', 'code' => $model->code]);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($redirectUrl)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(300)
            ->margin(10)
            ->build();

        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', $result->getMimeType());

        return $result->getString();
    }

    protected function isUrlReachable(string $url): bool
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_errno($ch);
        curl_close($ch);

        if ($err !== 0) {
            return false;
        }

        return $httpCode >= 200 && $httpCode < 400;
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}

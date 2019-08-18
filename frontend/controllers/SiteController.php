<?php
namespace frontend\controllers;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\City;
use common\models\Forecast;
use common\components\Helper;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
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
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }

    public function actionStats()
    {
        $data = [
            'dateStart' => Yii::$app->formatter->asDate('-1 day', 'php:d.m.Y'),
            'dateEnd' => Yii::$app->formatter->asDate('now', 'php:d.m.Y')
        ];
        return $this->render('stats', $data);
    }

    public function actionHistory()
    {
        $city = "Moscow";
        $currentCity = City::find()
            ->with(['country'])
            ->where(['name' => $city])
            ->asArray()
            ->one();

        if (empty($currentCity)) {
            $data['error'] = 'Sorry, but history for select city doesn\'t fount!';
        }

        $data['city'] = $currentCity;

        $forecasts = Forecast::find()
            ->where(['city_id' => $currentCity['id']])
            ->asArray()
            ->all();

        foreach ($forecasts as $forecast) {
            $date = Yii::$app->formatter->asDate($forecast['when_created'], 'php:M d, Y');
            $data['forecasts'][$date][] = $forecast;
        }

        return $this->render('history', $data);
    }

    public function actionDatatablesStats()
    {
        $request = Yii::$app->request;

        $forecasts = array();
        $totalForecasts = Forecast::find()
            ->select('city_id, COUNT(DISTINCT city_id) as cities')
            ->groupBy('city_id')
            ->sum('c.cities');


        if (!empty($totalForecasts)) {
//            $orderBy = "";
//            $requestOrder = $request->post('order');
//            $requestColumns = $request->post('columns');
//
//            if (!empty($requestOrder[0]) && !empty($requestColumns[$requestOrder[0]['column']])) {
//                $orderBy = $requestColumns[$requestOrder[0]['column']]['data'] . ' ' . $requestOrder[0]['dir'];
//            }

            $forecasts = Forecast::find()
                ->with(['city', 'city.country'])
                ->select('city_id, min(temperature), max(temperature), avg(temperature)')
                ->where([
                    'between',
                    'when_created',
                    Yii::$app->formatter->asDate($request->post('date_start'), 'php:U'),
                    Yii::$app->formatter->asDate($request->post('date_end'), 'php:U')
                ])
                ->groupBy('city_id')
//                ->orderBy($orderBy)
                ->limit($request->post('length'))
                ->offset($request->post('start'))
                ->asArray()
                ->all();
        }

        $output = array(
            "draw" => $request->post('draw'),
            "recordsTotal" => $totalForecasts,
            "recordsFiltered" => count($forecasts),
            "data" => []
        );

        $symbol = '&#8451;';
        if (!empty($forecasts)) {
            foreach ($forecasts as $forecast) {
                $output['data'][] = [
                    'country' => $forecast['city']['country']['name'],
                    'city' => $forecast['city']['name'],
                    'max_temperature' => Helper::convertFahrenheitToCelsius($forecast['max']) . Html::decode($symbol),
                    'min_temperature' => Helper::convertFahrenheitToCelsius($forecast['min']) . Html::decode($symbol),
                    'avg_temperature' => Helper::convertFahrenheitToCelsius($forecast['avg']) . Html::decode($symbol),
                    'actions' => '',
                ];
            }
        }

        return $this->asJson($output);
    }
}

<?php
namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
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
     * @inheritdoc
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

    public function actionIndex()
    {
        $posts = \common\models\Post::find()
                ->where(['status'=>1])
                ->orderBy('id DESC')
                ->limit(3)
                ->all();
        $categories = \common\models\Category::find()
                ->orderBy('name ASC')
                ->all();
        
        return $this->render('index',['posts'=>$posts, 'categories'=>$categories]);
    }
    
    public function actionPostCategory($id) {
        $posts = \common\models\Post::find()
                ->where(['status'=>1, 'category_id'=>$id])
                ->orderBy('id DESC')
                ->limit(5)
                ->all();
        
        $categories = \common\models\Category::find()
                ->orderBy('name ASC')
                ->all();
        
        return $this->render('postCategory', [
            'posts'=>$posts,
            'categories'=>$categories,
            ]);
    }
    
    public function actionPostSingle($id) {
        $post = \common\models\Post::find()
                ->where(['status'=>1, 'id'=>$id])
                ->one();
        
        $categories = \common\models\Category::find()
                ->orderBy('name ASC')
                ->all();
        $comments = \common\models\Comment::find()
                ->where(['status'=>1, 'post_id'=>$id])
                ->orderBy('id DESC')
                ->all();
        $model = new \common\models\Comment();
        if ($model->load(\Yii::$app->request->post())) {
            $model->post_id=$id;
            $model->status=0;
            $model->create_time=time();
            if (!\Yii::$app->user->isGuest) {
                $model->author=\Yii::$app->user->identity->username;
                $model->email=\Yii::$app->user->identity->email;
                $model->status=1;
            }
            if($model->validate()) {
                if($model->save()) {
                    if($model->status=1) {
                        \Yii::$app->session->setFlash('Success', 'Comment saved');
                    }else {
                        \Yii::$app->session->setFlash('Success', 'Comment saved, waiting moderation');
                    }
                }
            }
        }
        return $this->render('postSingle', [
            'post'=>$post,
            'categories'=>$categories,
            'comments'=>$comments,
            'model'=>$model,
        ]);
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}

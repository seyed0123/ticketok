<?php

namespace frontend\controllers;

use common\models\Ticket;
use common\models\User;
use common\models\UserTicket;
use mysql_xdevapi\Warning;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TicketController implements the CRUD actions for Ticket model.
 */
class TicketController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Ticket models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $userTickets = $userTickets = UserTicket::find()
            ->select('ticket_id')
            ->andWhere(['user_id'=> Yii::$app->user->id])
            ->column();
        $dataProvider = new ActiveDataProvider([
            'query' => Ticket::find()->andWhere(['id' => $userTickets])->andWhere(['status' =>Ticket::STATUS_SEND]),
            /*
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
            */
        ]);

        $ticketStatuses = UserTicket::find()
            ->select(['ticket_id', 'status'])
            ->where(['user_id' => Yii::$app->user->id])
            ->indexBy('ticket_id')
            ->asArray()
            ->all();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'ticketStatuses' => $ticketStatuses
        ]);
    }

    public function actionSent(){
        $dataProvider = new ActiveDataProvider([
            'query' => Ticket::find()->andWhere(['author_id' => Yii::$app->user->id]) ,
            /*
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
            */
        ]);

        return $this->render('sent', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Ticket model.
     * @param string $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if($model->author_id !== Yii::$app->user->id){
            $userTicket = UserTicket::findone([ 'user_id'=>Yii::$app->user->id,'ticket_id'=>$model->id ]);
            if($userTicket){
                $userTicket->status = UserTicket::STATUS_SEEN;
                $userTicket->save();


            }else{
                return $this->actionIndex();
            }
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Ticket model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Ticket();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Ticket model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $savedUserTickets = UserTicket::findAll(['ticket_id' =>$model->id]);
        $savedUsernames = [];
        foreach ($savedUserTickets as $userTicket) {
            $user = $userTicket->user;
            if ($user) {
                $savedUsernames[] = $user->username;
            }
        }
        $model->usernames = implode(', ', $savedUsernames);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Ticket model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Ticket model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Ticket the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Ticket::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

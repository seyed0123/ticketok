<?php

namespace frontend\controllers;

use common\models\Ticket;
use common\models\TicketSearch;
use common\models\User;
use common\models\UserTicket;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
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


        $searchModel = new TicketSearch();
        $dataProvider2 = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider2,
            'searchModel' => $searchModel,
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
        $ticketStatuses = UserTicket::find()
            ->select(['user_id', 'status','update_at'])
            ->where(['ticket_id' => $id])
            ->indexBy('user_id')
            ->asArray()
            ->all();


        foreach ($ticketStatuses as $ticketStatus){
            $ticketStatuses[$ticketStatus['user_id']] = ['username'=>User::find()->andWhere(['id' => $ticketStatus['user_id']])->one()->username,'status'=>UserTicket::getStatusLabels()[$ticketStatus['status']],'update_at'=>$ticketStatus['update_at']];
        }


        return $this->render('view', [
            'model' => $model,
            'ticketStatuses' => $ticketStatuses,
            'back' => $model->author_id === Yii::$app->user->id
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

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = $model->save(false);
                $flag = $flag && $model->handelSave(true) ;
                if ( $flag) {
                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $model->id]);
                }else{
                    $transaction->rollBack();
                }
            } catch (Exception $e) {
                Yii::error($e->getMessage());
                $transaction->rollBack();
            }
        }else{
            Yii::error($model->getErrors(), 'application');
            Yii::warning($model->usernames,'application');
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

        $savedUserTickets = $model->getUserTickets()->all();
        $savedIds = [];
        foreach ($savedUserTickets as $userTicket) {
            $savedIds[] = (string)$userTicket->user->id;
        }
        $model->usernames = $savedIds;
        if ($this->request->isPost && $model->load($this->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = $model->save(false);
                $flag = $flag && $model->handelSave() ;
                if ( $flag) {
                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $model->id]);
                }else{
                    $transaction->rollBack();

                }
            } catch (Exception $e) {
                Yii::error($e->getMessage());
                $transaction->rollBack();
            }
        }else{
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

    /**
     * @param $q
     * @param $id
     * @return \yii\web\Response
     */
    public function actionUserList($q = null, $id = null) {

        if (!is_null($q)) {
            $data = User::find()
                ->where(['like', 'username', $q])
                ->limit(20)->all();
            foreach ($data as $index=>$item){
                $out['results'][$index]=[
                    'id'=>$item->id,
                    'text'=>$item->username
                ];
            }


        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => User::findone($id)->username];
        }
        return $this->asJson($out);
    }
}

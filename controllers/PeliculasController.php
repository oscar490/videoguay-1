<?php

namespace app\controllers;

use app\models\Alquileres;
use app\models\Peliculas;
use app\models\PeliculasSearch;
use app\models\Socios;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\data\Sort;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * PeliculasController implements the CRUD actions for Peliculas model.
 */
class PeliculasController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    /**
     * Muestra un listado paginado de películas.
     * @return mixed
     */
    public function actionListado()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Peliculas::find(),
            // 'pagination' => [
            //     'pageSize' => 2,
            // ],
            // 'sort' => [
            //     'attributes' => [
            //         'codigo' => ['label' => 'Código'],
            //         'titulo' => ['label' => 'Título'],
            //         'precio_alq' => ['label' => 'Precio de alquiler'],
            //     ],
            // ],
        ]);

        $dataProvider->sort->attributes['todo'] = [
            'asc' => ['codigo' => SORT_ASC, 'titulo' => SORT_ASC, 'precio_alq' => SORT_ASC],
            'desc' => ['codigo' => SORT_DESC, 'titulo' => SORT_DESC, 'precio_alq' => SORT_DESC],
            'default' => SORT_ASC,
        ];

        return $this->render('listado', [
            'dataProvider' => $dataProvider,
        ]);
        // $pagination = new Pagination([
        //     'totalCount' => $peliculas->count(),
        //     'pageSize' => 2,
        // ]);
        // $sort = new Sort([
        //     'attributes' => [
        //         'codigo' => ['label' => 'Código'],
        //         'titulo' => ['label' => 'Título'],
        //         'precio_alq' => ['label' => 'Precio de alquiler'],
        //     ],
        // ]);
        // $peliculas = $peliculas
        //     ->orderBy($sort->orders)
        //     ->limit($pagination->limit)
        //     ->offset($pagination->offset)
        //     ->all();
        // return $this->render('listado', [
        //     'peliculas' => $peliculas,
        //     'pagination' => $pagination,
        //     'sort' => $sort,
        // ]);
    }

    /**
     * Alquila una película.
     * @return mixed
     */
    public function actionAlquilar()
    {
        $alquilarForm = new \app\models\AlquilarForm();

        if ($alquilarForm->load(Yii::$app->request->post()) && $alquilarForm->validate()) {
            $socio = Socios::findOne(['numero' => $alquilarForm->numero]);
            $pelicula = Peliculas::findOne(['codigo' => $alquilarForm->codigo]);
            $alquiler = new Alquileres([
                'socio_id' => $socio->id,
                'pelicula_id' => $pelicula->id,
                'scenario' => Alquileres::ESCENARIO_CREAR,
            ]);
            $alquiler->save();
            return $this->redirect(['index']);
        }

        return $this->render('alquilar', [
            'alquilarForm' => $alquilarForm,
        ]);
    }

    /**
     * Lists all Peliculas models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PeliculasSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Peliculas model.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $alquileres = Alquileres::find()
            ->with('socio')
            ->where(['pelicula_id' => $id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(10)
            ->all();

        return $this->render('view', [
            'model' => $this->findModel($id),
            'alquileres' => $alquileres,
        ]);
    }

    /**
     * Creates a new Peliculas model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Peliculas();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Peliculas model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Peliculas model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Peliculas model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return Peliculas the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Peliculas::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

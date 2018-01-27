<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\records\StudyingRecord */

$this->title = Yii::t('app', 'Create Studying Record');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Studying Records'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="studying-record-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

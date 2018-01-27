<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\records\SubjectRecord */

$this->title = Yii::t('app', 'Create Subject Record');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Subject Records'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-record-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

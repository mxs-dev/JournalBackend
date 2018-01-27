<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\records\ObserveRecord */

$this->title = Yii::t('app', 'Create Observe Record');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Observe Records'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="observe-record-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

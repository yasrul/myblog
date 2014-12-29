<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Post */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="post-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?php 
    $dataCategory=ArrayHelper::map(\common\models\Category::find()->asArray()->all(), 'id', 'name');
    echo $form->field($model, 'category_id')->dropDownList($dataCategory, ['prompt'=>'-Pilih Kategori']);
    ?>
    
    <?php
    echo $form->field($model, 'status')->dropDownList(['0'=>'Draff', '1'=>'Publish'], ['prompt'=>'']);
    ?>
    
    <?php /*
    <?= $form->field($model, 'create_time')->textInput() ?>

    <?= $form->field($model, 'update_time')->textInput() ?>

    <?= $form->field($model, 'user_id')->textInput() ?>
     */ ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

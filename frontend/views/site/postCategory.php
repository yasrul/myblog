<?php
use yii\bootstrap\Nav;

/* @var $this yii\web\View */
$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div>
        <h1>Yii Tutorial</h1>
        <p class="lead">Let's fun learn Yii framework 2.0</p>
        <p><a class="btn btn-lg btn-success" href="http://www.yiiframework.com">Are you ready ?</a></p>
    </div>
    
    <div class="row">
        <div class="col-lg-9">
            <?php
            foreach($posts as $post) {
            echo '<div>';
            echo '<h2>'.$post->title.'</h2>';
            echo '<p>'.substr($post->content, 0, 300).'...</p>';
            echo '<p><small>Post By '.$post->user->username.' at '.date('F j,Y,g:i a',$post->create_time).'</small></p>';
            echo '<p><a class="btn btn-default" href="'.\Yii::$app->urlManager->createUrl(['site/post-single', 'id'=>$post->id]).'">Read more &raquo;</a></p>';
            echo '</div>';
            }
            ?>
        </div>
        <div class="col-lg-3">
            <h2>Category</h2>
            <?php
            $items=[];
            foreach ($categories as $category) {
                $items[]=['label'=>$category->name, 'url'=>\Yii::$app->urlManager->createUrl(['site/post-category', 'id'=>$category->id])];
            }
            echo Nav::widget([
                'items' => $items,
                ]);
            ?>
        </div>
    </div>

</div>

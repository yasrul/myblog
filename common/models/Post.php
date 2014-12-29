<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "post".
 *
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property integer $category_id
 * @property integer $status
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $user_id
 *
 * @property Comment[] $comments
 * @property Category $category
 * @property User $user
 */
class Post extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content', 'category_id', 'status'], 'required'],
            [['content'], 'string'],
            [['category_id', 'status', 'create_time', 'update_time', 'user_id'], 'integer'],
            [['title'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'content' => 'Content',
            'category_id' => 'Category ID',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'user_id' => 'User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['post_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
    public function behaviors()
    {
        return [
            'timestamp'=>[
                'class'=> \yii\behaviors\TimestampBehavior::className(),
                'attributes'=> [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT=>['create_time', 'update_time'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE=>['update_time'],
                ],
            ],
            'autouserid' => [
                'class'=> \yii\behaviors\BlameableBehavior::className(),
                'attributes'=> [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT=>['user_id']
                ]
            ]
        ];
    }
}

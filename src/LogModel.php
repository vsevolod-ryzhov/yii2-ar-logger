<?php

declare(strict_types=1);

namespace vsevolodryzhov\yii2ArLogger;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "{{%logger}}".
 *
 * @property string $id
 * @property string $user_id
 * @property string $created_at
 * @property string $action
 * @property string $model_name
 * @property string $model_id
 * @property string $attributes
 * @property string $description
 */
class LogModel extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%logger}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['action', 'model_name', 'model_id'], 'required'],
            [['user_id', 'model_id'], 'integer'],
            [['created_at'], 'safe'],
            [['description'], 'string'],
            [['model_name', 'action'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_log' => '#',
            'user_id' => '# user',
            'created_at' => 'Created at',
            'action' => 'Action name',
            'model_name' => 'Model name',
            'model_id' => 'Model ID',
            'attributes' => 'Attributes array',
            'description' => 'Description'
        ];
    }

    public function beforeValidate()
    {
        // strict log change
        if (!$this->isNewRecord) {
            return false;
        }
        if (!empty($this->attributes) && is_array($this->attributes)) {
            $this->attributes = htmlspecialchars(json_encode($this->attributes));
        }
        return parent::beforeValidate();
    }

    public function afterFind()
    {
        $this->attributes = htmlspecialchars_decode($this->attributes);
        parent::afterFind();
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()')
            ],
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => 'user_id',
            ],
        ];
    }
}
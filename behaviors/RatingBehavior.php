<?php
/**
 * @link https://github.com/Chiliec/yii2-vote
 * @author Vladimir Babin <vovababin@gmail.com>
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace chiliec\vote\behaviors;

use chiliec\vote\models\Rating;
use chiliec\vote\models\AggregateRating;
use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

class RatingBehavior extends Behavior
{
    /**
     * @inheritdoc
     */
    public function attach($owner)
    {
        if (!$owner instanceof ActiveRecord) {
            throw new InvalidConfigException(Yii::t('vote', 'Please attach this behavior to the instance of the ActiveRecord class'));
        }
        parent::attach($owner);
    }

    /**
     * @inheritdoc
     */
    public function getLikes()
    {
        return $this->owner
            ->hasMany(Rating::className(), [
                'target_id' => $this->owner->primaryKey()[0]
            ])
            ->select(['id', 'target_id', 'user_id', 'user_ip', 'value', 'date'])
            ->where('model_id = :modelId AND value = :value', [
                ':modelId' => Rating::getModelIdByName($this->owner->className()),
                ':value' => Rating::VOTE_LIKE,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function getDislikes()
    {
        return $this->owner
            ->hasMany(Rating::className(), [
                'target_id' => $this->owner->primaryKey()[0]
            ])
            ->select(['id', 'target_id', 'user_id', 'user_ip', 'value', 'date'])
            ->where('model_id = :modelId AND value = :value', [
                ':modelId' => Rating::getModelIdByName($this->owner->className()),
                ':value' => Rating::VOTE_DISLIKE,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function getLikesCount()
    {
        return $this->owner
            ->hasOne(AggregateRating::className(), [
                'target_id' => $this->owner->primaryKey()[0],
            ])
            ->select('likes')
            ->where('model_id = :modelId', [
                ':modelId' => Rating::getModelIdByName($this->owner->className())
            ])
            ->scalar();
    }

    /**
     * @inheritdoc
     */
    public function getDislikesCount()
    {
        return $this->owner
            ->hasOne(AggregateRating::className(), [
                'target_id' => $this->owner->primaryKey()[0],
            ])
            ->select('dislikes')
            ->where('model_id = :modelId', [
                ':modelId' => Rating::getModelIdByName($this->owner->className())
            ])
            ->scalar();
    }

    /**
     * @inheritdoc
     */
    public function getRating()
    {
        return $this->owner
            ->hasOne(AggregateRating::className(), [
                'target_id' => $this->owner->primaryKey()[0],
            ])
            ->select('rating')
            ->where('model_id = :modelId', [
                ':modelId' => Rating::getModelIdByName($this->owner->className())
            ])
            ->scalar();
    }
}

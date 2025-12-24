<?php

namespace app\models\query;

use yii\db\ActiveQuery;

class UsersQuery extends ActiveQuery
{
    public function notDeleted()
    {
        return $this->andWhere(['deleted_at' => null]);
    }

    public function all($db = null)
    {
        return parent::all($db);
    }

    public function one($db = null)
    {
        return parent::one($db);
    }
}

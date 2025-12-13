<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Quests;

/**
 * QuestsSearch represents the model behind the search form of `app\models\Quests`.
 */
class QuestsSearch extends Quests
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'creator_id'], 'integer'],
            [['name', 'description', 'cover_image_url', 'created_at', 'delete_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Quests::find();

        // По умолчанию не показываем удаленные квесты (Soft Delete)
        $query->andWhere(['delete_at' => null]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'creator_id' => $this->creator_id,
        ]);

        // Фильтр по дате (игнорируем время, если передана только дата)
        if ($this->created_at) {
            $query->andFilterWhere(['DATE(created_at)' => $this->created_at]);
        }

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'cover_image_url', $this->cover_image_url]);

        return $dataProvider;
    }
}
<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class QuestStationsSearch extends QuestStations
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'quest_id'], 'integer'],
            [['name', 'type', 'qr_identifier', 'created_at'], 'safe'],
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
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = QuestStations::find()
            ->andWhere(['deleted_at' => null]); // soft delete фильтрация

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // если валидация не прошла — возвращаем все (без удаления)
            return $dataProvider;
        }

        // Точные фильтры
        $query->andFilterWhere([
            'id' => $this->id,
            'quest_id' => $this->quest_id,
            'type' => $this->type,
        ]);

        // LIKE-фильтры
        $query
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'qr_identifier', $this->qr_identifier])
            ->andFilterWhere(['like', 'created_at', $this->created_at]);

        return $dataProvider;
    }
}

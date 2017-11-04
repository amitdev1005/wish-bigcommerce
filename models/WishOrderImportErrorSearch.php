<?php

namespace frontend\modules\wishmarketplace\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\wishmarketplace\models\WishOrderImportError;

/**
 * WishOrderImportErrorSearch represents the model behind the search form about `frontend\modules\integration\models\WishOrderImportError`.
 */
class WishOrderImportErrorSearch extends WishOrderImportError
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'wish_order_id', 'merchant_id'], 'integer'],
            [['reason', 'created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = WishOrderImportError::find()->where(['merchant_id' => MERCHANT_ID]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['created_at' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'wish_order_id' => $this->wish_order_id,
            'merchant_id' => $this->merchant_id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'reason', $this->reason]);

        return $dataProvider;
    }
}

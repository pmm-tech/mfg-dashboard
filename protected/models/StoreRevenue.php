<?php

/**
 * This is the model class for table "store_revenue".
 *
 * The followings are the available columns in table 'store_revenue':
 * @property integer $id
 * @property string $store_code
 * @property string $date
 * @property double $current_revenue
 * @property integer $last_updated
 */
class StoreRevenue extends CActiveRecord
{
	public $total;
	public $date_to;
	public $point;
	public $i = 0;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return StoreRevenue the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'store_revenue';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('store_code, date, current_revenue, last_updated', 'required'),
			array('last_updated', 'numerical', 'integerOnly' => true),
			array('current_revenue', 'numerical'),
			array('store_code', 'length', 'max' => 16),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, store_code, date, current_revenue, last_updated, date_to', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'store_code' => 'Kode Toko',
			'date' => 'Tanggal Mulai',
			'date_to' => 'Tanggal Akhir',
			'current_revenue' => 'Omset',
			'last_updated' => 'Terakhir Update',
			'point' => 'Poin'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;
		if (!empty($this->date_to)) {
			$criteria->condition = 'date >= :date AND date <= :date_to AND code IS NOT NULL';
			$criteria->params = array(
				':date' => $this->date,
				':date_to' => $this->date_to
			);
		} else {
			$criteria->compare('date', $this->date, true);
		}
		$criteria->select = 't.id, t.store_code, s.code, min(t.date) AS date, "' . $this->date_to . '" AS date_to, 
				sum(t.current_revenue) AS total,  sum(t.current_revenue) AS current_revenue, max(t.last_updated) AS last_updated';
		$criteria->group = 'store_code';
		$criteria->join = 'LEFT JOIN store s ON t.store_code = s.code';
		$criteria->order = 'total desc';
		$criteria->compare('id', $this->id);
		$criteria->compare('store_code', $this->store_code, true);
		//$criteria->compare('date',$this->date,true);
		$criteria->compare('current_revenue', $this->current_revenue);
		$criteria->compare('last_updated', $this->last_updated);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => 10
			),
		));
	}

	public function afterFind()
	{
		$temp = $this->store_code;
		$this->store_code = '';
		$total_omset = $this->getTotalRevenue();
		$this->store_code = $temp;
		// Avoid division by zero when there is no revenue data
		$this->point = ($total_omset > 0 && isset($this->total)) ? round(($this->total / $total_omset) * 100, 2) : 0;
	}



	/**
	 * Get formatted date with string
	 */
	public function getDate()
	{
		if (empty($this->date)) {
			return '';
		}
		$tmp = explode('-', $this->date);
		if (count($tmp) !== 3) {
			return $this->date;
		}
		return date('j F Y', mktime(0, 0, 0, (int)$tmp[1], (int)$tmp[2], (int)$tmp[0]));
	}

	/**
	 * Get formatted date with string
	 */
	public function getDateTo()
	{
		if (!empty($this->date_to)) {
			$tmp = explode('-', $this->date_to);
			return ' s.d. ' . date('j F Y', mktime(0, 0, 0, $tmp[1], $tmp[2], $tmp[0]));
		} else return '';
	}

	/**
	 * Get store name
	 */
	public function getStoreName()
	{
		$model = StoreIp::model()->findByAttributes(array(
			'store_code' => $this->store_code,
		));
		if ($model)
			return $model->name;
		else return 'Unknown';
	}

	public function getOmsetByGroup()
	{
		$sql = 'SELECT koalisi, sum(current_revenue) AS omset  
				FROM store_revenue r LEFT JOIN store s ON r.store_code = s.code';
		$condition[] = 'koalisi IS NOT NULL';
		$params = array();
		if (!empty($this->date)) {
			$condition[] = 'date >= :from';
			$params[':from'] = $this->date;
		}

		if (!empty($this->date_to)) {
			$condition[] = 'date <= :to';
			$params[':to'] = $this->date_to;
		}

		if (!empty($condition))
			$sql .= ' WHERE ' . implode(' AND ', $condition);
		$sql .= ' GROUP BY koalisi';

		$cmd = $this->getDbConnection()->createCommand($sql);

		return $cmd->queryAll(true, $params);
	}


	/**
	 * Get total store revenue
	 */
	public function getTotalRevenue()
	{
		$param = array();
		if (!empty($this->date) && !empty($this->store_code)) {
			if (!empty($this->date_to)) {
				$sql = 'SELECT SUM(current_revenue) as total FROM store_revenue 
						WHERE date >= :date AND date <= :date_to  AND store_code = :code';
				$param = array(
					':date' => $this->date,
					':date_to' => $this->date_to,
					':code' => $this->store_code
				);
			} else {
				if (!empty($this->date_to)) {
					$sql = 'SELECT SUM(current_revenue) as total FROM store_revenue 
							WHERE date >= :date AND date <= :date_to';
					$param = array(
						':date' => $this->date,
						':date_to' => $this->date_to,
						// 							':code'=>$this->store_code
					);
				} else {
					$sql = 'SELECT SUM(current_revenue) as total FROM store_revenue WHERE date = :date';
					$param = array(
						':date' => $this->date,
						// 							':date_to'=>$this->date_to,
						// 							':code'=>$this->store_code
					);
				}
			}
		} else if (!empty($this->date)) {
			if (!empty($this->date_to)) {
				$sql = 'SELECT SUM(current_revenue) as total FROM store_revenue WHERE date >= :date AND date <= :date_to';
				$param = array(
					':date' => $this->date,
					':date_to' => $this->date_to,
					// 						':code'=>$this->store_code
				);
			} else {
				$sql = 'SELECT SUM(current_revenue) as total FROM store_revenue WHERE date = :date';
				$param = array(
					':date' => $this->date,
					// 						':date_to'=>$this->date_to,
					// 						':code'=>$this->store_code
				);
			}
		} else if (!empty($this->store_code)) {
			$sql = 'SELECT SUM(current_revenue) as total FROM store_revenue WHERE store_code = :code';
			$param = array(
				// 					':date'=>$this->date,
				// 					':date_to'=>$this->date_to,
				':code' => $this->store_code
			);
		} else {
			$sql = 'SELECT SUM(current_revenue) as total FROM store_revenue';
			$param = array();
		}
		//var_dump($sql);
		$cmd = Yii::app()->db->createCommand($sql);
		$total = $cmd->queryScalar($param);

		if (!empty($total))
			return $total;
		else return 0;
	}
}

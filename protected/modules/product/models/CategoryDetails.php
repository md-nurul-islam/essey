<?php

/**
 * This is the model class for table "cims_category_details".
 *
 * The followings are the available columns in table 'cims_category_details':
 * @property integer $id
 * @property string $category_name
 * @property string $category_description
 *
 * The followings are the available model relations:
 * @property ProductDetails[] $productDetails
 * @property ProductStockEntries[] $productStockEntries
 * @property ProductStockSales[] $productStockSales
 */
class CategoryDetails extends CActiveRecord {

    public $pageSize = 20;
    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'cims_category_details';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('category_name', 'required'),
            array('category_name', 'unique'),
            array('category_name', 'length', 'max' => 120),
            array('category_description', 'length', 'max' => 250),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, category_name, category_description', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'productDetails' => array(self::HAS_MANY, 'ProductDetails', 'category_id'),
            'productStockEntries' => array(self::HAS_MANY, 'ProductStockEntries', 'category_id'),
            'productStockSales' => array(self::HAS_MANY, 'ProductStockSales', 'category_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'category_name' => 'Category Name',
            'category_description' => 'Description',
            'status' => 'Status',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('category_name', $this->category_name, true);
        $criteria->compare('category_description', $this->category_description, true);
        $criteria->compare('status', 1);

//        if (!Yii::app()->user->isSuperAdmin) {
//            $criteria->compare('t.store_id', Yii::app()->user->storeId);
//        }

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CategoryDetails the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return array for Data Grid Headers customized attribute labels (name=>label)
     * remove the attributes don't needed in the Grid
     */
    public function dataGridHeaders() {
        return array(
            'id' => array('label' => 'ID', 'sortable' => 'true', 'width' => 50, 'hidden' => TRUE),
            'category_name' => array('label' => 'Category Name', 'sortable' => 'true', 'width' => 50),
            'category_description' => array('label' => 'Description', 'sortable' => 'true', 'width' => 180),
            'status' => array('label' => 'Status', 'sortable' => 'true', 'width' => 80)
        );
    }

    public function dataGridFilters() {
        return array(
            'category_name' => array('id' => 'billnumber', 'class' => 'easyui-textbox', 'label' => 'Category Name: ', 'style' => 'width:80px;'),
            'status' => array('id' => 'status', 'class' => 'easyui-combobox', 'label' => 'Status',
                'data-options' => "valueField: 'id', textField: 'text', url: '/product/category/getStatusComboData' ",
                'panelHeight' => 70,
                'style' => 'width:80px; cursor: pointer;'),
        );
    }

    public function statusComboData() {

        return array(
            array(
                'id' => '',
                'text' => 'Select',
            ),
            array(
                'id' => '1',
                'text' => 'Active',
            ),
            array(
                'id' => '0',
                'text' => 'Inactive',
            ),
        );
    }

    public function dataGridRows($params = array()) {
        
        $store_id = 1;
        
        if(!Yii::app()->user->isSuperAdmin) {
            $store_id = Yii::app()->user->storeId;
        }

        $offset = 0;
        if (isset($params['offset']) && $params['offset'] > 0) {
            $offset = $params['offset'];
        }

        $order = 'id DESC';
        if (isset($params['order']) && !empty($params['order'])) {
            $order = $params['order'];
        }

        $command = Yii::app()->db->createCommand()
                ->from($this->tableName() . ' t')
                ->offset($offset)
                ->limit($this->pageSize)
                ->order($order)
                ->andWhere('store_id = :sid', array(':sid' => $store_id))
        ;

        $sub_command = Yii::app()->db->createCommand()
                ->select('count( DISTINCT t.id )')
                ->from($this->tableName() . ' t')
                ->andWhere('store_id = :sid', array(':sid' => $store_id))
        ;
        
        $filter_keys = array_keys($this->dataGridFilters());
        if (isset($params['where']) && !empty($params['where'])) {
            $new_command_objs = DataGridHelper::processFilterableVars($command, $params['where'], $filter_keys, 't', $sub_command);
            $command = $new_command_objs[0];
            $sub_command = $new_command_objs[1];
        }

        $command->select('t.id, t.category_name, t.category_description, CASE t.status WHEN "1" THEN "Active" ELSE "Inactive" END AS `status`, (' . $sub_command->getText() . ') AS total_rows');

        return $command->queryAll();
    }

}

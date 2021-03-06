<?php

/**
 * This is the model class for table "cims_tmp_cart".
 *
 * The followings are the available columns in table 'cims_tmp_cart':
 * @property integer $id
 * @property string $grand_total
 * @property integer $cart_type
 * @property string $discount
 * @property string $vat
 */
class TmpCart extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'cims_tmp_cart';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('grand_total', 'required'),
            array('cart_type', 'numerical', 'integerOnly' => true),
            array('grand_total, discount, vat', 'length', 'max' => 13),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, grand_total, cart_type, discount, vat', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'grand_total' => 'Grand Total',
            'cart_type' => 'Cart Type',
            'discount' => 'Discount',
            'vat' => 'Vat',
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
        $criteria->compare('grand_total', $this->grand_total, true);
        $criteria->compare('cart_type', $this->cart_type);
        $criteria->compare('discount', $this->discount, true);
        $criteria->compare('vat', $this->vat, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return TmpCart the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function getCart($id = 0, $type = 1) {

        $command = Yii::app()->db->createCommand()
                ->from($this->tableName() . ' t')
                ->join(TmpCartItems::model()->tableName() . ' ci', 't.id = ci.cart_id');

        $command->select('
            t.id,
            t.grand_total,
            t.cart_type,
            t.discount AS total_discount,
            t.vat AS total_vat,
            ci.product_details_id,
            ci.reference_number,
            ci.price,
            ci.quantity,
            ci.sub_total,
            ci.discount AS item_discount,
            ci.vat AS item_vat
            ');

        if ($id > 0) {
            $command->andWhere('t.id = :cid', array(':cid' => $id));
        }

        if ($type > 0) {
            $command->andWhere('t.cart_type = :ctype', array(':ctype' => $type));
        }

        return $command->queryAll();
    }

}

<?php

class ExchangeController extends Controller {

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array(
                    'index',
                    'get_sales',
                    'view',
                    'create',
                    'update',
                    'product_stock_info',
                    'print',
                    'getdata',
                    'getStatusComboData'
                ),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id) {
        $this->redirect(array('print', 'sales_id' => $id));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {

        $this->pageTitle = Yii::app()->name . ' - Exchange';

        $model = new ExchangeProducts;

        if (!Yii::app()->user->isSuperAdmin) {
            $store_id = Yii::app()->user->storeId;
        }

        $sale_model = new ProductStockSales;
        $this->pageHeader = 'Exchange Product';

        $this->render('create', array(
            'model' => $model,
            'sale_model' => $sale_model,
        ));
    }

    public function exchangeProduct($sales_id, $total = 0) {

        $model = new ExchangeProducts;
        $edit = FALSE;

        $this->render('ex_form', array(
            'model' => $model,
            'main_prod_total' => $total,
            'sales_id' => $sales_id,
            'edit' => $edit,
        ));
    }

    public function actionPrint() {

        $id = Yii::app()->request->getParam('sales_id');

        $model = new ExchangeProducts;
        $model_main = $model->getExchanges($id);
//        echo '<pre>';
//        var_dump($model_main);
//        exit;
        $model_ex = $model->getExchanges($id, true);

        if (!Yii::app()->user->isSuperAdmin) {
            $store_id = Yii::app()->user->storeId;
        } else {
            $store_id = 1;
        }

        $store = StoreDetails::model()->findByPk($store_id);

        $this->render('print', array(
            'model_main' => $model_main,
            'model_ex' => $model_ex,
            'store' => $store,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {

        $model = new ExchangeProducts;
        $model_data = $model->getSalesInfo(NULL, $id);
        $edit = TRUE;

        if (isset($_POST['ExchangeProducts'])) {

            $bill_number = $_POST['ExchangeProducts']['billnumber'];
            $sale_date = date('Y-m-d', strtotime($_POST['ExchangeProducts']['sale_date']));
            $due_payment_date = date('Y-m-d', strtotime($_POST['ExchangeProducts']['due_payment_date']));
            $note = $_POST['ExchangeProducts']['note'];
            $payment_type = $_POST['ExchangeProducts']['payment_method'];

            $dis_amount = $_POST['ExchangeProducts']['dis_amount'];
            $grand_total_payable = $_POST['ExchangeProducts']['grand_total_payable'];
            $grand_total_paid = $_POST['ExchangeProducts']['grand_total_paid'];
            $grand_total_balance = $_POST['ExchangeProducts']['grand_total_balance'];

            if (!Yii::app()->user->isSuperAdmin) {
                $store_id = Yii::app()->user->storeId;
            } else {
                $store_id = 1;
            }

            $limit = sizeof($_POST['ExchangeProducts']['product_details_id']);
            $count = 0;

            for ($i = 0; $i < $limit; $i++) {

                $prod_sale = new ExchangeProducts;

                $sale_id = $model_data[0]->sales_id;
                $ref_num = $_POST['ExchangeProducts']['ref_num'][$i];
                $prod_id = $_POST['ExchangeProducts']['product_details_id'][$i];

                $model = $prod_sale->getSalesInfo(NULL, $sale_id, $ref_num, $prod_id);

                $model->billnumber = $bill_number;
                $model->sale_date = $sale_date;
                $model->due_payment_date = $due_payment_date;
                $model->note = $note;
                $model->payment_method = $payment_type;

                $model->dis_amount = $dis_amount;
                $model->grand_total_payable = $grand_total_payable;
                $model->grand_total_paid = $grand_total_paid;
                $model->grand_total_balance = $grand_total_balance;
                $model->store_id = $store_id;

                if ($model->update()) {
                    $count++;
                }
            }

            if ($count == $limit) {
                Yii::app()->user->setFlash('success', 'Products successfully sold.');
                $this->redirect(array('print', 'sales_id' => $model->sales_id));
            }
        }

        $ar_cart = array();

        $ar_product_details_id = array();
        $ar_product_details_name = array();
        $ar_ref_num = array();
        $ar_quantity = array();
        $ar_purchase_price = array();
        $ar_selling_price = array();
        $ar_item_subtotal = array();
        $ar_serial_num = array();
        $ar_cur_stock = array();

        foreach ($model_data as $row) {
            $ar_product_details_id[] = $row->product_details_id;
            $ar_product_details_name[] = $row['productDetails']->product_name;
            $ar_ref_num[] = $row->ref_num;
            $ar_quantity[] = $row->quantity;
            $ar_selling_price[] = $row->item_selling_price;
            $ar_item_subtotal[] = $row->item_subtotal;
            $ar_serial_num[] = $row->serial_num;
            $ar_cur_stock[] = 0;

            $model->billnumber = $row->billnumber;
            $model->sale_date = $row->sale_date;
            $model->supplier_id = $row->supplier_id;
            $model->dis_amount = $row->dis_amount;
            $model->grand_total_payable = $row->grand_total_payable;
            $model->grand_total_paid = $row->grand_total_paid;
            $model->grand_total_balance = $row->grand_total_balance;
            $model->due_payment_date = $row->due_payment_date;
            $model->payment_method = $row->payment_method;
            $model->note = $row->note;
            $model->sales_id = $row->sales_id;
        }

        $ar_cart['product_details_id'] = $ar_product_details_id;
        $ar_cart['product_details_name'] = $ar_product_details_name;
        $ar_cart['ref_num'] = $ar_ref_num;
        $ar_cart['quantity'] = $ar_quantity;
        $ar_cart['selling_price'] = $ar_selling_price;
        $ar_cart['item_subtotal'] = $ar_item_subtotal;
        $ar_cart['serial_num'] = $ar_serial_num;
        $ar_cart['cur_stock'] = $ar_cur_stock;

        $this->render('update', array(
            'model' => $model,
            'ar_cart' => $ar_cart,
            'edit' => $edit,
        ));
    }

    public function actionGet_sales() {

        $billnumber = Yii::app()->request->getParam('billnumber');

        $model = new ProductStockSales;
        $model_data = $model->getSaleData(0, $billnumber);

        echo $this->renderPartial('_ex_form', array(
            'model' => $model_data,
                ), TRUE);
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * Manages all models.
     */
    public function actionIndex() {

        $this->pageTitle = Yii::app()->name . ' - Exchange List';
        $this->pageHeader = 'Exchange List';

        $this->render('index');
    }

    /*
     * Grid functions
     */

    public function actionGetdata() {

        foreach (DataGridHelper::$_ar_non_filterable_vars as $nfv_key => $nfv_var_name) {
            ${$nfv_var_name} = Yii::app()->request->getParam($nfv_key);
        }

        $rows = array();
        $offest = 0;

        if (${DataGridHelper::$_ar_non_filterable_vars['page']} > 1) {
            $offest = (${DataGridHelper::$_ar_non_filterable_vars['page']} - 1) * ${DataGridHelper::$_ar_non_filterable_vars['rows']};
        }

        $productExchange = new ExchangeProducts();

        $productExchange->pageSize = 20;
        $query_params = array(
            'offset' => $offest,
            'order' => ${DataGridHelper::$_ar_non_filterable_vars['sort']} . ' ' . ${DataGridHelper::$_ar_non_filterable_vars['order']},
            'where' => $_POST,
        );

        $result['rows'] = $productExchange->dataGridRows($query_params);
//        var_dump($result['rows']);exit;
        $result["total"] = 0;

        if (($result['rows'])) {
            $result["total"] = $result['rows'][0]['total_rows'];
        }
        echo CJSON::encode($result);
        Yii::app()->end();
    }

    public function actionGetStatusComboData() {
        echo CJSON::encode(ProductStockSales::model()->statusComboData());
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return ExchangeProducts the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = ExchangeProducts::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param ExchangeProducts $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'product-exchange-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    /**
     * gets product stock information and latest cost and selling price.
     */
    public function actionProduct_stock_info() {

        if ((Yii::app()->user->isGuest) || (!Yii::app()->request->isAjaxRequest)) {
            throw new CHttpException(403, 'Access Forbidden.');
        }

        $response = array();
        $ref_num = Yii::app()->request->getParam('ref_num');
        $prod_id = Yii::app()->request->getParam('prod_id');
        $prod_id = (!empty($prod_id)) ? $prod_id : '';

        $cur_stock = 0;

        $model = new ProductStockEntries();

        $model = $model->getProductStockInfo($prod_id, $ref_num, true);

        if (!empty($model)) {
            $romatted_data = $this->formatProdInfo($model);
            $response['response'] = $romatted_data;
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    private function generateSalesId() {

        $data = Yii::app()->db->createCommand()->select('MAX(id) AS id')->from('cims_product_stock_sales')->queryRow();

        $max_id = (strlen($data['id']) < 2) ? '0' . ($data['id'] + 1) : ($data['id'] + 1);

        return $max_id = 'SD' . $max_id;
    }

    private function formatProdInfo($prods) {

        $response = array();

        foreach ($prods as $row) {

            $_data['product_id'] = $row['productDetails']->id;
            $_data['product_name'] = $row['productDetails']->product_name;
            $_data['price'] = ( empty($row['productDetails']->selling_price) || ($row['productDetails']->selling_price <= 0) ) ? $row->selling_price : $row['productDetails']->selling_price;
            $_data['cur_stock'] = $row['productDetails']['productStockAvails']->quantity;
            $response[] = $_data;
        }

        return $response;
    }

}

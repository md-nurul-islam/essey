<div class="row">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'product-stock-exchange-form',
        // Please note: When you enable ajax validation, make sure the corresponding
        // controller action is handling ajax validation correctly.
        // There is a call to performAjaxValidation() commented in generated controller code.
        // See class documentation of CActiveForm for details on this.
        'enableAjaxValidation' => false,
        'htmlOptions' => array(
            'class' => 'form-horizontal',
        )
    ));
    ?>

    <div class="col-sm-12">

        <input type="hidden" id="cart_id" value="0" />
        <input type="hidden" id="global_vat" name="global_vat" value="<?php echo Settings::$_vat; ?>" />
        <input type="hidden" id="global_vat_mode" name="global_vat_mode" value="<?php echo Settings::$_vat_mode; ?>" />
        <input type="hidden" id="global_discount" name="global_discount" value="<?php echo Settings::$_discount; ?>" />
        <input type="hidden" id="global_discount_mode" name="global_discount_mode" value="<?php echo Settings::$_discount_mode; ?>" />


        <div class="col-sm-12">
            <div class="box box-info">
                <div class="box-body">

                    <div class="form-group">
                        <?php echo $form->labelEx($sale_model, 'billnumber', array('class' => 'col-sm-3 control-label')); ?>
                        <div class="col-sm-8">
                            <?php
                            echo $form->textField($sale_model, 'billnumber', array(
                                'size' => 20,
                                'maxlength' => 120,
                                'class' => 'form-control',
                                'placeholder' => 'Billnumber',
                            ));
                            ?>
                            <?php echo $form->error($sale_model, 'billnumber', array('class' => 'alert alert-danger')); ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 show-later">
        
        <div class="col-sm-6">
            <div class="box box-info">
                <div class="box-body sold-items"></div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="box box-info">
                <div class="box-body">

                    <div class="form-group">
                        <div class="col-md-12">
                            <?php
                            echo CHtml::textField('ref_num', '', array(
                                'size' => 25,
                                'maxlength' => 150,
                                'class' => 'form-control',
                                'placeholder' => 'Type Reference Number',
                            ));
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12" id="div_product_list">

                        </div>
                    </div>

                    <div class="panel panel-default">

                        <div class="printable">
                            <table class="table table-hover table-bordered table-responsive table-condensed exchange-with">

                                <thead>
                                    <tr>
                                        <th class="col-sm-6">Item</th>
                                        <th class="col-sm-1">Price</th>
                                        <th class="col-sm-2">Qty</th>
                                        <th class="col-sm-2">Total</th>
                                        <th class="col-sm-1"></th>
                                    </tr>
                                </thead>

                                <tbody id="cart-row"></tbody>

                                <tfoot id="cart-total">
                                    <tr>
                                        <th class="vat_cell">Vat</th>
                                        <th class="vat_cell_val">0.00</th>
                                        <th class="discount_cell">Discount</th>
                                        <th class="discount_cell_val">0.00</th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="2">Total Items</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>

                        <div class="clearfix"></div>

                        <div class="col-md-12" id="div_buttons_wrapper">

                            <div class="col-md-8">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-primary form-control bg-green border-0">Save</button>
                                </div>

                                <div class="col-md-6">
                                    <button type="button" class="btn btn-warning form-control cart-print bg-orange border-0">Print</button>
                                </div>

                                <div class="col-md-12">
                                    <button type="button" class="btn btn-danger form-control border-0">Cancel</button>
                                </div>
                            </div>

                            <div class="col-md-4 pull-right">
                                <button type="button" class="btn btn-success form-control btn-payment bg-green border-0">Payment</button>
                            </div>


                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <?php $this->endWidget(); ?>
</div>

<div id="quantityModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            
            <div class="modal-body">
                <form id="item_quantity" class="form-horizontal">
                    
                    <input type="hidden" id="row_index_val" value="" />
                    
                    <div class="form-group">
                        <?php echo CHtml::label('Exchanging Quantity', 'exchanging_qty', array('class' => 'col-sm-4 control-label')); ?>
                        <div class="col-sm-7">
                            <?php
                            echo CHtml::textField('exchanging_qty', '', array(
                                'class' => 'form-control',
                                'placeholder' => 'Exchanging Quantity',
                            ));
                            ?>
                        </div>
                    </div>

                </form>

            </div>

            <div class="modal-footer">
                <div class="col-md-11">
                    <button id="btn_exchanging_qty" type="button" class="btn btn-success btn-flat" data-dismiss="modal">Apply</button>
                </div>
            </div>
            
        </div>

    </div>
</div>

<?php $this->renderPartial('//modals/_price'); ?>
<?php $this->renderPartial('//modals/_vat'); ?>
<?php $this->renderPartial('//modals/_exchange_payment'); ?>
<?php $this->renderPartial('//print/print'); ?>

<style type="text/css">
    .card_information {
        display: none;
    }
    table.payment tr th {
        cursor: pointer;
    }
    table.payment tr:first-child > th, table.payment tr:nth-child(2) > th, table.payment tr:nth-child(3) > th, table.payment tr:nth-child(4) > th {
        border-bottom: 1px solid #ccc !important;
    }
    table.payment tr th:first-child {
        border-right: 1px solid #ccc !important;
        border-left: 1px solid #ccc !important;
    }

    #div_buttons_wrapper {
        padding: 0;
        margin-top: 20px;
    }
    #div_buttons_wrapper .col-md-6,
    #div_buttons_wrapper .col-md-12,
    #div_buttons_wrapper .col-md-8 {
        padding-left: 0;
        padding-right: 0;
    }
    #div_buttons_wrapper .col-md-4 {
        padding-left: 10px;
        padding-right: 0;
    }
    #div_buttons_wrapper .col-md-4 button {
        height: 68px;
    }
    #div_buttons_wrapper button {
        border-radius: 0;
    }
    label {
        cursor: pointer;
    }
    .vat_options li {
        margin-right: 20px;
    }
    .vat_options li:last-child {
        margin-right: 0;
    }
    .form-horizontal p {
        margin-top: 3px; 
        color: #666666; 
    }
    .vat_cell, .discount_cell {
        cursor: pointer;
        text-decoration: underline;
    }
    tfoot#cart-total tr:first-child {
        background-color: #37a000;
        color: #ffffff;
    }
    tfoot#cart-total tr:last-child {
        background-color: orange;
        color: #ffffff;
    }
    .modal-header, .modal-footer {
        background-color: #3c8dbc;
        color: #ffffff;
    }
    .label {
        font-size: 14px;
    }
    i.fa-trash-o {
        color: #CC0000;
        cursor: pointer;
        font-size: 18px;
    }
    #div_product_list {
        display: none;
    }
    #div_product_list .radio-inline label {
        font-weight: 600;
        margin-right: 10px;
    }
    #div_product_list .radio-inline label:last-child {
        margin-right: 0;
    }
    .cart_qty {
        height: 30px;
    }
    #cart-row td {
        cursor: pointer;
    }
</style>

<?php
Yii::app()->clientScript->registerCoreScript('jquery');
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile('/js/custom/exchange_cart.js', CClientScript::POS_END);
$cs->registerScriptFile('/js/custom/print.js', CClientScript::POS_END);
?>

<script type="text/javascript">
    $(document).ready(function () {
        $(".select2").select2({
            minimumResultsForSearch: Infinity,
            width: '100%'
        });
        $(".select2").tooltip();
        $(".select2").tooltip('disable');
    });
</script>
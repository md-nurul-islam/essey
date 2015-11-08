<?php
/* @var $this ManageController */
/* @var $model ProductDetails */
/* @var $form CActiveForm */
?>
<style type="text/css">
    .ui-menu .ui-menu-item{
        background-color: #eeeeee;
    }
</style>
<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'product-details-form',
        // Please note: When you enable ajax validation, make sure the corresponding
        // controller action is handling ajax validation correctly.
        // There is a call to performAjaxValidation() commented in generated controller code.
        // See class documentation of CActiveForm for details on this.
        'enableAjaxValidation' => false,
    ));
    ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'category_id'); ?>
        <?php echo $form->hiddenField($model, 'category_id'); ?>
        <?php
        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'name' => 'category_name',
            'value' => $category_name,
            'source' => $this->createUrl('category/autocomplete'),
            'options' =>
            array(
                'minLength' => 'a',
                'showAnim' => 'fold',
                'select' => "js:function(event, data) {
                            $('#ProductDetails_category_id').val(data.item.id);
                        }",
            ),
            'htmlOptions' => array(
                'style' => 'height:20px;',
                'size' => 60,
                'placeholder' => 'Type Category Name..'
            ),
        ));
        ?>
        <?php echo $form->error($model, 'category_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'supplier_id'); ?>
        <?php echo $form->hiddenField($model, 'supplier_id'); ?>
        <?php
        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'name' => 'supplier_name',
            'value' => $supplier_name,
            'source' => $this->createUrl('/supplier/manage/autocomplete'),
            'options' =>
            array(
                'minLength' => 'a',
                'showAnim' => 'fold',
                'select' => "js:function(event, data) {
                            $('#ProductDetails_supplier_id').val(data.item.id);
                        }",
            ),
            'htmlOptions' => array(
                'style' => 'height:20px;',
                'size' => 60,
                'placeholder' => 'Type Supplier Name..'
            ),
        ));
        ?>
        <?php echo $form->error($model, 'supplier_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'product_name'); ?>
        <?php echo $form->textField($model, 'product_name', array('size' => 60, 'maxlength' => 255)); ?>
        <?php echo $form->error($model, 'product_name'); ?>
    </div>

    <div class="clearfix"></div>

    <div class="row horizontal">

        <?php echo CHtml::label('Available Qualities', ''); ?>

        <ul class="checkbox-list full-width">
            <?php foreach ($grades as $grade) { ?>
                <li>
                    <?php
                    $checked = FALSE;
                    if (isset($ar_product_id)) {
                        if (in_array($grade->id, $ar_product_id)) {
                            $checked = TRUE;
                        }
                    }
                    ?>
                    <?php echo CHtml::checkBox('ProductGrade[grade_id][]', $checked, array('value' => $grade->id)); ?>
                    <?php echo CHtml::label($grade->name, '', array()); ?>
                </li>
            <?php } ?>
        </ul>

    </div>

    <div class="clearfix"></div>

    <div class="row">
        <?php echo $form->labelEx($model, 'purchase_price'); ?>
        <?php echo $form->textField($model, 'purchase_price', array('size' => 10, 'maxlength' => 10)); ?>
        <?php echo $form->error($model, 'purchase_price'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'selling_price'); ?>
        <?php echo $form->textField($model, 'selling_price', array('size' => 10, 'maxlength' => 10)); ?>
        <?php echo $form->error($model, 'selling_price'); ?>
    </div>

    <div class="row">
        <?php
        $data = array('0' => 'Inactive', '1' => 'Active');
        ?>
        <?php echo $form->labelEx($model, 'status'); ?>
        <?php echo $form->dropDownList($model, 'status', $data); ?>
        <?php echo $form->error($model, 'status'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('id' => 'btn-submit')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->
<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-raypay" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) : ?>
          <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) : ?>
      <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
    <?php endif; ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form id="form-raypay" method="post" enctype="multipart/form-data" action="<?php echo $action; ?>" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="raypay_status"><?php echo $text_status; ?></label>
                <div class="col-sm-10">
                  <select name="raypay_status" class="form-control">
                    <?php if ($raypay_status) : ?>
                      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                      <option value="0"><?php echo $text_disabled; ?></option>
                    <?php else : ?>
                      <option value="1"><?php echo $text_enabled; ?></option>
                      <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php endif; ?>
                  </select>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="raypay_user_id"><?php echo $text_user_id; ?></label>
                <div class="col-sm-10">
                  <input name="raypay_user_id" type="text" value="<?php echo $raypay_user_id; ?>" class="form-control" />
                  <?php if (!empty($error_user_id)) : ?>
                    <div class="text-danger"><?php echo $error_user_id; ?></div>
                  <?php endif; ?>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="raypay_marketing_id"><?php echo $text_marketing_id; ?></label>
                <div class="col-sm-10">
                  <input name="raypay_marketing_id" type="text" value="<?php echo $raypay_marketing_id; ?>" class="form-control" />
                  <?php if (!empty($error_marketing_id)) : ?>
                    <div class="text-danger"><?php echo $error_marketing_id; ?></div>
                  <?php endif; ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sandbox"><?php echo $text_sandbox ?></label>
                <div class="col-sm-10">
                  <select name="raypay_sandbox" id="input-sandbox" class="form-control">
                    <option value="no" <?php if ($raypay_sandbox == 'no') echo 'selected="selected"'?>><?php echo $entry_sandbox_no ?></option>
                    <option value="yes" <?php if ($raypay_sandbox == 'yes') echo 'selected="selected"'?>><?php echo $entry_sandbox_yes ?></option>
                  </select>
                  <span><?php echo $text_sandbox_help ?></span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="raypay_successful_message"><?php echo $text_successful_message; ?></label>
                <div class="col-sm-10">
                  <textarea name="raypay_successful_message" class="form-control"><?php echo $entry_payment_successful_message_default; ?></textarea>
                  <span><?php echo $text_successful_message_help ?></span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="raypay_failed_message"><?php echo $text_failed_message; ?></label>
                <div class="col-sm-10">
                  <textarea name="raypay_failed_message" class="form-control"><?php echo $entry_payment_failed_message_default; ?></textarea>
                  <span><?php echo $text_failed_message_help ?></span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="raypay_order_status_id"><?php echo $text_order_status; ?></label>
                <div class="col-sm-10">
                  <select name="raypay_order_status_id" class="form-control">
                    <?php foreach ($order_statuses as $order_status) : ?>
                      <?php if ($order_status['order_status_id'] == $raypay_order_status_id) : ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                      <?php else : ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="raypay_sort_order"><?php echo $text_sort_order; ?></label>
                <div class="col-sm-10">
                  <input name="raypay_sort_order" type="text" value="<?php echo $raypay_sort_order; ?>" class="form-control" />
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>

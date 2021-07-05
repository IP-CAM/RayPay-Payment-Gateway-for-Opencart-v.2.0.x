<?php if ($error_warning) : ?>
<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
<?php else: ?>
<form action="<?php echo $action; ?>" method="post">
  <input type="hidden" name="token" value="<?php echo $token; ?>" />
  <input type="hidden" name="TerminalID" value="<?php echo $terminal_id; ?>" />
  <div class="buttons">
    <div class="pull-right">
      <input type="submit" value="<?php echo $button_confirm; ?>" class="btn btn-primary" />
    </div>
  </div>
</form>
<?php endif; ?>

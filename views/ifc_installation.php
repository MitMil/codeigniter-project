<div class="container">
    <!-- row -->
    <div class="row">
        <div class="wrapper">
            <h2 class="text-center"><?php echo _('iFC Installation');?></h2>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div id="resetSettingsSn-container">
                        <?php if (isset($msg) && $msg) : ?>
                            <div class="alert <?php echo $msg_class; ?> alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="<?php echo _("Close"); ?>"><span aria-hidden="true">&times;</span></button><?php $lang_version = $msg;
                            echo _("{$lang_version}");?></div>
                        <?php endif; ?>
                        <?php if ($flag_showform) : ?>
                        <form action="" method="post" id="installform">
                            <div class="form-group">
                                <label for="serial_number" class="col-sm-3 control-label"><?php echo _('Enter Serial Number'); ?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="serial_number" name="serial_number" placeholder="" value="<?php echo (isset($_POST['serial_number']) && $_POST['serial_number']) ? $_POST['serial_number'] : '' ; ?>" required="required">
                                    <button type="submit" name="submit" id="installform_btn" class="btn btn-primary btn-md" style="margin-top: 20px;"><?php echo _('Submit'); ?></button>
                                </div>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <div class="panel panel-default" id="log-panel" style="display: none">
                <div class="panel-heading">
                    <?php echo _('Log'); ?>
                </div>
                <div class="panel-body">
                    <div id="containerDiv"></div>
                </div>
            </div>
        </div>
    </div>
    <!--/ row -->
</div>

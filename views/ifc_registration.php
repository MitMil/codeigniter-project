<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <title>iFC Registration</title>
        <!-- jQuery -->
        <script src="<?php echo base_url(); ?>assets/sbadmin2/bower_components/jquery/dist/jquery.min.js"></script>
        <!-- Bootstrap Core CSS -->
        <link href="<?php echo base_url(); ?>assets/sbadmin2/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>

    <body>
        <!-- Container -->
        <div class="container-fluid">
            <!-- row -->
            <div class="row">
                <h2 class="text-center"><?php echo _('iFC Registration');?></h2>
            </div>
            <!--/ row -->
            <hr />

            <div style="display: none;">
                <?php echo validation_errors('<div class="text-danger">', '</div>'); ?>
            </div>
            <!-- row -->
            <div class="row">
                <!-- form -->
                <form method="post" action="<?php echo base_url(); ?>users/ifc_registration" class="form-horizontal">
                    <div class="col-md-offset-3 col-md-6">
                        <?php if (isset($msg) && $msg) { ?>
                            <div class="alert <?php echo $msg_class; ?> alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="<?php echo _("Close"); ?>"><span aria-hidden="true">&times;</span></button><?php $lang_version = $msg;
                            echo _("{$lang_version}");?></div>
                        <?php } ?>
                        <div class="space">
                            <div class="form-group">
                                <label for="serial_number" class="col-sm-4 control-label"><?php echo _('Serial Number'); ?></label>
                                <div class="col-sm-8">
                                    <input type="textdomain" class="form-control" id="serial_number" name="serial_number" value="<?php echo ($serial_number) ? $serial_number : set_value('serial_number'); ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="name" class="col-sm-4 control-label"><?php echo _('Farm Name'); ?></label>
                                <div class="col-sm-8">
                                    <input type="textdomain" class="form-control" id="name" name="name" placeholder="<?php echo _('Enter Farm Name'); ?>" value="<?php echo set_value('name'); ?>" required>
                                        <?php echo form_error('name', '<div class="text-danger">', '</div>'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email" class="col-sm-4 control-label"><?php echo _('Email'); ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="email" name="email" placeholder="<?php echo _('Enter E-mail Address'); ?>" value="<?php echo set_value('email'); ?>" required>
                                    <?php echo form_error('email', '<div class="text-danger">', '</div>'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="address" class="col-sm-4 control-label"><?php echo _('Address'); ?></label>
                                <div class="col-sm-8">
                                    <textarea rows="3" cols="30" class="form-control" id="address" name="address" placeholder="<?php echo _('Enter Address'); ?>" ><?php echo set_value('address'); ?></textarea>
                                    <?php echo form_error('address', '<div class="text-danger">', '</div>'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="city" class="col-sm-4 control-label"><?php echo _('City'); ?></label>
                                <div class="col-sm-8">
                                    <input type="textdomain" class="form-control" id="city" name="city" placeholder="<?php echo _('Enter City'); ?>" value="<?php echo set_value('city'); ?>" required>
                                    <?php echo form_error('city', '<div class="text-danger">', '</div>'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="country" class="col-sm-4 control-label"><?php echo _('Country'); ?></label>
                                <div class="col-sm-8">
                                    <input type="textdomain" class="form-control" id="country" name="country" placeholder="<?php echo _('Enter country'); ?>" value="<?php echo set_value('country'); ?>" required>
                                    <?php echo form_error('country', '<div class="text-danger">', '</div>'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="cap" class="col-sm-4 control-label"><?php echo _('CAP'); ?></label>
                                <div class="col-sm-8">
                                    <input type="cap" class="form-control" id="cap" name="cap" placeholder="<?php echo _('Enter CAP'); ?>"  value="<?php echo set_value('cap'); ?>" required>
                                    <?php echo form_error('cap', '<div class="text-danger">', '</div>'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="phone" class="col-sm-4 control-label"><?php echo _('Phone'); ?></label>
                                <div class="col-sm-8">
                                    <input type="phone" class="form-control" id="phone" name="phone" placeholder="<?php echo _('Enter Phone'); ?>" value="<?php echo set_value('phone'); ?>" required>
                                    <?php echo form_error('phone', '<div class="text-danger">', '</div>'); ?>
                                </div>
                            </div>

                            <div class="col-sm-4 col-sm-offset-4">
                                <button type="submit" name="submit" class="btn btn-success btn-lg"><?php echo _('Save'); ?></button>&nbsp;&nbsp;&nbsp;&nbsp;or <a  href="<?php echo base_url().'login/logout'; ?>" class="btn btn-link"><?php echo _('Logout');?></a>
                            </div>
                        </div>
                    </div>
                <!--/ form -->
                </form>
            </div>
            <hr />
            <!-- /row -->
        <!--/ Container -->
        </div>
        <!-- Bootstrap Core JavaScript -->
        <script src="<?php echo base_url(); ?>assets/sbadmin2/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    </body>
</html>

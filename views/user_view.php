<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <?php
            $page_name_postfix = ($user_data['username']) ? ' | '.$user_data['username'] : '' ;
            ?>
            <div class="page-header">
                <h1><?php echo _('User Information').$page_name_postfix; ?></h1>
            </div>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading"><?php echo _('Basic'); ?> <?php echo _('User Information'); ?></div>
                <div class="panel-body">
                    <div class="row">

                        <div class="col-lg-7 col-md-offset-1">
                            <div class="form-group clearfix">
                                <label for="username" class="col-sm-3 control-label"><?php echo _('Username'); ?></label>
                                <div class="col-sm-9">
                                    <?php echo $user_data['username']; ?>
                                </div>
                            </div>

                            <div class="form-group clearfix">
                                <label for="roles" class="col-sm-3 control-label"><?php echo _('Role'); ?></label>
                                <div class="col-sm-9">
                                    <div>
                                    <?php
                                    if ($user_data['roles']) {
                                        foreach ($user_data['roles'] as $key => $value) {
                                            echo '<label class="label label-default">'.$value['role_name'].'</label>&nbsp;&nbsp;';
                                        }
                                    }
                                    ?>
                                    </div>

                                </div>
                            </div>

                            <div class="form-group clearfix">
                                <label for="name" class="col-sm-3 control-label"><?php echo _('Name'); ?></label>
                                <div class="col-sm-9">
                                    <?php echo $user_data['name']; ?>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group clearfix">
                                <label for="email" class="col-sm-3 control-label"><?php echo _('Email'); ?></label>
                                <div class="col-sm-9">
                                    <?php echo $user_data['email']; ?>
                                </div>
                            </div>

                            <div class="form-group clearfix">
                                <label for="address" class="col-sm-3 control-label"><?php echo _('Address'); ?></label>
                                <div class="col-sm-9">
                                    <?php echo $user_data['address']; ?>
                                </div>
                            </div>

                            <div class="form-group clearfix">
                                <label for="city" class="col-sm-3 control-label"><?php echo _('City'); ?></label>
                                <div class="col-sm-9">
                                    <?php echo $user_data['city']; ?>
                                </div>
                            </div>

                            <div class="form-group clearfix">
                                <label for="state" class="col-sm-3 control-label"><?php echo _('State'); ?></label>
                                <div class="col-sm-9">
                                    <?php echo $user_data['state']; ?>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group clearfix">
                                <label for="cap" class="col-sm-3 control-label"><?php echo _('CAP'); ?></label>
                                <div class="col-sm-9">
                                    <?php echo $user_data['cap']; ?>
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <label for="phone" class="col-sm-3 control-label"><?php echo _('Phone'); ?></label>
                                <div class="col-sm-9">
                                    <?php echo $user_data['phone']; ?>
                                </div>
                            </div>

                            <div class="form-group clearfix">
                                <label for="language" class="col-sm-3 control-label"><?php echo _('Language'); ?></label>
                                <div class="col-sm-9">
                                    <?php echo $user_data['lang'].' ('.$user_data['code'].')'; ?>
                                </div>
                            </div>

                            <div class="form-group clearfix">
                                <label for="date_format" class="col-sm-3 control-label"><?php echo _('Date Format'); ?></label>
                                <div class="col-sm-9">
                                    <?php
                                    if (isset($user_data['date_format']) && $user_data['date_format']) {
                                        $dateformat_choices = dateformat_choices();
                                        echo $dateformat_choices[$user_data['date_format']];
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="form-group clearfix">
                                <label for="time_format" class="col-sm-3 control-label"><?php echo _('Time Format'); ?></label>
                                <div class="col-sm-9">
                                    <?php
                                    if ($user_data['time_format']) {
                                        if ($user_data['time_format'] == 'H:i:s') {
                                            echo '24-hours';
                                        } else {
                                            echo '12-hours';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="form-group clearfix">
                                <label  class="col-sm-3 control-label"><?php echo _('Profile'); ?></label>
                                <div class="col-sm-9">
                                <?php
                                if ($user_data['profile']) {
                                    if (file_exists(APPLICATION_PATH.'/assets/uploads/'.$user_data['profile'])) { ?>
                                        <img src="<?php echo base_url().'assets/uploads/'.$user_data['profile'] ?>" height="100px" width="100px">
                                    <?php
                                    } else { ?>
                                        <i class="<?php echo $user_data['profile']; ?> fa-3x"></i>
                                    <?php
                                    }
                                } ?>
                                </div>
                            </div>

                            <div class="form-group clearfix">
                                <label for="name" class="col-sm-3 control-label"><?php echo _('Insert Time'); ?></label>
                                <div class="col-sm-9">
                                    <?php echo display_datetime($user_data['insert_datetime'], $user_data['date_format'], $user_data['time_format']); ?>
                                </div>
                            </div>

                            <div class="form-group clearfix">
                                <label for="name" class="col-sm-3 control-label"><?php echo _('Last Updated'); ?></label>
                                <div class="col-sm-9">
                                    <?php echo display_datetime($user_data['update_datetime'], $user_data['date_format'], $user_data['time_format']); ?>
                                </div>
                            </div>


                        </div><!-- /.col-lg-6 -->


                    </div><!-- /.row (nested) -->

                </div><!-- /.panel-body -->

            </div><!-- /.panel -->

        </div><!-- /.col-lg-12 -->

    </div><!-- /.row -->
</div>
<!-- /#page-wrapper -->

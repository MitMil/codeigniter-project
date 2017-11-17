<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <?php
            $page_name_postfix = ($role_data['name']) ? ' | '.$role_data['name'] : '' ;
            ?>
            <div class="page-header">
                <h1><?php echo _('Role Information').$page_name_postfix; ?></h1>
            </div>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading"><?php echo _('Basic'); ?> <?php echo _('Role Information'); ?></div>
                <div class="panel-body">
                    <div class="row">

                        <div class="col-lg-7 col-md-offset-1">

                            <div class="form-group clearfix">
                                <label for="name" class="col-sm-3 control-label"><?php echo _('Name'); ?></label>
                                <div class="col-sm-9">
                                    <?php echo $role_data['name']; ?>
                                </div>
                            </div>

                            <div class="form-group clearfix">
                                <label for="name" class="col-sm-3 control-label"><?php echo _('No. of Users'); ?></label>
                                <div class="col-sm-9">
                                    <a class="link" href="<?php echo base_url('users').'?role_id='.$role_data['role_id']; ?>"><?php echo $role_data['count_assign'];?></a>
                                </div>
                            </div>

                            <div class="form-group clearfix">
                                <label for="name" class="col-sm-3 control-label"><?php echo _('Insert Time'); ?></label>
                                <div class="col-sm-9">
                                    <?php echo $role_data['insert_datetime']; ?>
                                </div>
                            </div>

                            <div class="form-group clearfix">
                                <label for="name" class="col-sm-3 control-label"><?php echo _('Last Updated'); ?></label>
                                <div class="col-sm-9">
                                    <?php echo $role_data['update_datetime']; ?>
                                </div>
                            </div>



                        </div><!-- /.col-lg-6 -->
                        <div class="col-lg-4">
                            <?php echo validation_errors('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="<?php echo _("Close"); ?>"><span aria-hidden="true">&times;</span></button>', '</div>'); ?>
                        </div><!-- /.col-lg-6 -->

                    </div><!-- /.row (nested) -->

                </div><!-- /.panel-body -->

            </div><!-- /.panel -->

        </div><!-- /.col-lg-12 -->

    </div><!-- /.row -->
</div>
<!-- /#page-wrapper -->

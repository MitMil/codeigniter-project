
<div id="page-wrapper">

    <div class="row">
        <div class="col-lg-12">
        <?php
        if (insert_restriction('module')) { ?>
            <div class="" style="float: right;margin-top: 10px;">
                <a href="<?php echo base_url().'modules/insert/'; ?>" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp;<?php echo _('Add'); ?> <?php echo _('Module'); ?></a>
            </div><?php
        } ?>
            <div class="page-header">
                <h1><?php echo _('Modules'); ?></h1>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    <?php echo _('All'); ?> <?php echo _('Modules'); ?>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">

                    <div class="msg_alert alert <?php echo $msg_class; ?> alert-dismissible <?php echo ($msg) ? '' : 'hide'; ?>" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="<?php echo _('Close'); ?>">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <?php $lang_version = $msg;
                        echo _("{$lang_version}");?>
                    </div>
                    <div class="dataTable_wrapper table-responsive module-table-responsive">
                        <table class="table table-striped table-bordered table-hover moduledataTable">
                        <thead>
                            <tr class="info">
                                <th><?php echo _('Module Name'); ?></th>
                                <th><?php echo _('Module URL'); ?></th>
                                <th><?php echo _('Last Updated'); ?></th>
                                <th><?php echo _('Actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($results) && ($results)) {

                                foreach ($results as $modules) {
                                ?>
                                    <tr class="odd gradeA" id="tr_<?php echo $modules['module_id']; ?>">
                                        <td><?php
                                                echo ($modules['parent']) ? ' -- ': '';
                                                echo $modules['module_name']; ?></td>
                                        <td><?php echo $modules['module_url']; ?></td>
                                        <td><?php echo display_datetime($modules['update_datetime']); ?></td>

                                        <td>
                                            <div class="btn-group" role="group" >
                                                <a href="<?php echo base_url().'modules/view?id='.$modules['module_id']; ?>" class="btn btn-warning btn-sm"><i class="fa fa-info-circle"></i></a>
                                                <a href="<?php echo base_url().'modules/insert?id='.$modules['module_id']; ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                                                <?php if (insert_restriction('module')) { ?>
                                                    <a data-toggle="modal" data-target="#confirm-delete" href="#" data-href="<?php echo $modules['module_id']; ?>" class="btn btn-danger btn-sm"><i class="fa fa-remove"></i></a>
                                                <?php } ?>
                                            </div>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else { ?>
                                    <tr>
                                        <td colspan="4"><?php echo _('No Data Found.'); ?></td>
                                    </tr>
                            <?php
                            } ?>
                        </tbody>

                        </table>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>

        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /#page-wrapper -->

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-danger">
                <?php echo _('Delete Confirmation'); ?>
            </div>
            <div class="modal-body">
                <p><?php echo _('Are You Sure, You Want to Delete?'); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _('Cancel'); ?></button>
                <a href="#" class="btn btn-danger danger btn_moduledelete_confirm"><?php echo _('Delete'); ?></a>
            </div>
        </div>
    </div>
</div>


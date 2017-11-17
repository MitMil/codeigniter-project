
<div id="page-wrapper">

    <div class="row">
        <div class="col-lg-12">
            <div class="" style="float: right;margin-top: 10px;"><a href="<?php echo base_url().'roles/insert/'; ?>" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp;<?php echo _('Insert'); ?></a></div>

            <div class="page-header">
                <h1><?php echo _('Roles'); ?></h1>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    <?php echo _('All'); ?> <?php echo _('Roles'); ?>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="msg_alert alert <?php echo $msg_class; ?> alert-dismissible <?php echo ($msg) ? '' : 'hide'; ?>" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="<?php echo _("Close"); ?>">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <?php $lang_version = $msg;
                        echo _("{$lang_version}");?>
                    </div>
                        <div class="dataTable_wrapper table-responsive">
                        <table class="table table-striped table-bordered table-hover listdataTable">
                            <thead>
                                <tr class="info">
                                    <th><?php echo _('Role ID'); ?></th>
                                    <th><?php echo _('Role Name'); ?></th>
                                    <th><?php echo _('Last Updated'); ?></th>
                                    <th width="25%"><?php echo _('Action'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (isset($results) && ($results)) {
                                $child_array = array();
                                foreach ($results as $roles) {
                                    if ($roles['parent'] != null) {
                                        $child_array[] = $roles;
                                        continue;
                                    }
                                ?>
                                <tr class="odd gradeA" id="tr_<?php echo $roles['role_id']; ?>">
                                    <td><?php echo $roles['role_id']; ?></td>
                                    <td>
                                        <?php
                                        echo $roles['name'];
                                        if ($roles['count_assign']) {
                                        ?>
                                            <a class="link" href="<?php echo base_url('users').'?role_id='.$roles['role_id']; ?>">(<?php echo $roles['count_assign'];?>)</a>
                                        <?php
                                        } ?>
                                    </td>
                                    <td><?php echo display_datetime($roles['update_datetime']); ?></td>
                                    <td><a href="<?php echo base_url().'modules/assign?role_id='.$roles['role_id'];?>" class="btn btn-default btn-sm"><?php echo _('Modules'); ?></a> </td>
                                </tr>
                            <?php
                                }
                            } else { ?>
                                    <tr>
                                        <td colspan="3"><?php echo _('No Data Found.'); ?></td>
                                    </tr>
                            <?php
                            } ?>
                            </tbody>

                        </table>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
            <?php
            if (isset($child_array) && ($child_array)) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?php echo _('User'); ?> | <?php echo _('Roles'); ?>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="dataTable_wrapper table-responsive">
                        <table class="table table-striped table-bordered table-hover listdataTable">
                        <thead>
                            <tr class="info">
                                <th><?php echo _('Role ID'); ?></th>
                                <th><?php echo _('Role Name'); ?></th>
                                <th><?php echo _('Last Updated'); ?></th>
                                <th><?php echo _('Action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($child_array as $roles) { ?>
                            <tr class="odd gradeA" id="tr_<?php echo $roles['role_id']; ?>">
                                <td><?php echo $roles['role_id']; ?></td>
                                <td>
                                    <?php echo $roles['name'];
                                    if ($roles['count_assign']) {
                                    ?>
                                        <a class="link" href="<?php echo base_url('users').'?role_id='.$roles['role_id']; ?>">(<?php echo $roles['count_assign'];?>)</a>
                                    <?php
                                    } ?>
                                </td>
                                <td><?php echo $roles['update_datetime']; ?></td>
                                <td>
                                    <div class="btn-group" role="group" >
                                        <a href="<?php echo base_url().'modules/assign?role_id='.$roles['role_id'];?>" class="btn btn-default btn-sm"><?php echo _('Modules'); ?></a>
                                        <a href="<?php echo base_url().'roles/view?id='.$roles['role_id']; ?>" class="btn btn-warning btn-sm"><i class="fa fa-info-circle"></i></a>
                                        <a href="<?php echo base_url().'roles/insert?id='.$roles['role_id']; ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                                        <a data-toggle="modal" data-target="#confirm-delete" href="#" data-href="<?php echo $roles['role_id']; ?>" class="btn btn-danger btn-sm"><i class="fa fa-remove"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        } ?>
                        </tbody>

                        </table>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
            <?php
            } ?>
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
                <a href="#" class="btn btn-danger danger btn_roledelete_confirm"><?php echo _('Delete'); ?></a>
            </div>
        </div>
    </div>
</div>

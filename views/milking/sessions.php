<div id="page-wrapper">

    <div class="row">
        <div class="col-lg-12">
            <div class="page-header">
                <h1><?php echo _('Milking sessions'); ?></h1>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    <?php echo _('All'); ?> <?php echo _('Milking Sessions'); ?>
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
                                <th><?php echo _('idms'); ?></th>
                                <th><?php echo _('idpannello'); ?></th>
                                <th><?php echo _('idimilk'); ?></th>
                                <th><?php echo _('idpacchetto'); ?></th>
                                <th><?php echo _('idanimal'); ?></th>
                                <th><?php echo _('status'); ?></th>
                                <th><?php echo _('number_animal'); ?></th>
                                <th><?php echo _('pedometro'); ?></th>
                                <th><?php echo _('data_enter'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (isset($sessions) && ($sessions)) {
                            foreach ($sessions as $session) { ?>
                            <tr class="odd gradeA" id="tr_<?php if(isset($session['idms']) && $session['idms']) { echo $session['idms']; } ?>">

                                <td><?php if(isset($session['idms']) && $session['idms']) { echo $session['idms']; } ?></td>
                                <td><?php if(isset($session['idpannello']) && $session['idpannello']) { echo $session['idpannello']; }?></td>
                                <td><?php if(isset($session['idimilk']) && $session['idimilk']) { echo $session['idimilk']; } ?></td>
                                <td><?php if(isset($session['idpacchetto']) && $session['idpacchetto']) { echo $session['idpacchetto']; }?></td>
                                <td><?php if(isset($session['idanimal']) && $session['idanimal']) { echo $session['idanimal']; }?></td>
                                <td><?php if(isset($session['status']) && $session['status']) { echo $session['status']; }?></td>
                                <td><?php if(isset($session['number_animal']) && $session['number_animal']) { echo $session['number_animal']; }?></td>
                                <td><?php if(isset($session['pedometro']) && $session['pedometro']) { echo $session['pedometro']; }?></td>
                                <td><?php if(isset($session['data_enter']) && $session['data_enter']) { echo display_datetime($session['data_enter']); }?></td>
                            </tr>
                            <?php
                            }
                        } else { ?>
                            <tr>
                                <td colspan="9"><?php echo _('No Data Found.'); ?></td>
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
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /#page-wrapper -->

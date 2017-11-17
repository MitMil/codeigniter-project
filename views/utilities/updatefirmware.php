<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="page-header">
                <h1><?php echo _('Utilities - Update Firmware iMilk'); ?></h1>
            </div>
        </div>
    </div>
    <!-- /.row -->

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading"><?php echo _('Upload Firmware file'); ?></div>
                <div class="panel-body">
                    <div class="row">
                        <?php
                            if(isset($error)): ?>
                            <div class="alert alert-danger">
                                <strong>Error!</strong><br /><?php echo $error; ?>
                            </div>
                        <?php
                            endif;

                            // OPEN Form
                            $attributes = array('class' => 'form-horizontal', 'role' => 'form');
                            echo form_open_multipart('utilities/updatefirmware', $attributes);
                        ?>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="iddevice" class="col-sm-2 control-label"><?php echo _('Device'); ?> *</label>
                                <div class="col-sm-9">
                                    <select name="iddevice" class="form-control">
                                        <?php
                                        foreach ($device_list as $key => $device_data) {
                                            $selected = (isset($iddevice) && $iddevice == $device_data['iddevice']) ? 'selected="selected"' : '';
                                            echo "<option value='" . $device_data['iddevice'] . "'". $selected."'>" . $device_data['IP'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="firmware" class="col-sm-2 control-label"> </label>
                                <div class="col-sm-9">
                                    <input type="file" name="firmware" size="20">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2 col-sm-offset-2">
                                    <input type="hidden" name="submit" value="upload" />
                                    <input type="submit" value="<?php echo _('upload'); ?>" />
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <?php echo _('Log Firmware uploaded'); ?>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">

                    <div class="dataTable_wrapper table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr class="info">
                                <th><?php echo _('iddf'); ?></th>
                                <th><?php echo _('Firmware'); ?></th>
                                <th><?php echo _('Data Upload'); ?></th>
                                <th><?php echo _('Data End'); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (isset($firmware_logs) && ($firmware_logs)):
                                foreach ($firmware_logs as $log): ?>
                                    <tr class="odd gradeA" id="tr_<?php echo $log['iddf']; ?>">
                                        <td><?php echo $log['iddf']; ?></td>
                                        <td><?php echo $log['firmware_tipo']; ?> <small>(<?php echo round($log['filesize']/1024); ?> Kb)</small><br />
                                            <small><?php echo _('Download:');?> <b><?php echo $log['filename']; ?></b> (<a href="#">.hex</a> - <a href="#">.gul</a>)</small>
                                        </td>
                                        <td><?php echo display_datetime($log['data_upload']); ?></td>
                                        <td><?php if( is_null($log['data_completed']) ): ?>
                                            <div class="progress">
                                                <div id="f_progress_bar_<?php echo $log['iddf']; ?>" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                                            </div>
                                            <div id="f_progress_info_<?php echo $log['iddf']; ?>"></div>
                                            <?php else: ?>
                                                <?php echo display_datetime($log['data_completed']); ?><br />
                                                <?php if($log['error'] != "0"): ?>
                                                    <small class="text-danger"><?php echo _('Completed:'); ?> <?php echo $log['perc_sent']; ?> % - <?php echo _('Error:');?> <?php echo $log['error']; ?></small>
                                                <?php else: ?>
                                                    <small class="text-success"><?php echo _('Completed:'); ?> <?php echo $log['perc_sent']; ?> % - <?php echo _('Error:');?> <?php echo $log['error']; ?></small>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php
                                endforeach;
                            else: ?>
                                <tr>
                                    <td colspan="4"><?php echo _('No Data Found.'); ?></td>
                                </tr>
                                <?php
                            endif; ?>
                            </tbody>

                        </table>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
    </div>
</div>

<script type="text/javascript">

    function makeProgress(iddf){

        $.ajax({
            url:"/utilities/getpercinprogress",
            type: "GET",
            dataType: "json",
            data: { iddf: iddf},
            success: function(result){
                //console.log(result);
                $("#f_progress_bar_" + iddf).css("width", result.perc_sent + "%").text(result.perc_sent + " %");
                $("#f_progress_info_" + iddf).html("<strong>Error:</strong> " + result.error);
            },
            error: function(richiesta,stato,errori) {
                $("#f_progress_info_" + iddf).html("<strong>Chiamata fallita:</strong>"+stato+" "+errori);
            }
        });

        // Wait for sometime before running this script again
        setTimeout( function() {
                makeProgress(iddf)
            }, 1000);
    }

    // run progress bar for UPDATE in progress
    <?php
    if (isset($firmware_logs) && ($firmware_logs)):
        foreach ($firmware_logs as $log):
            if($log['in_progress'] ==1):
    ?>
            makeProgress(<?php echo $log['iddf']; ?>);
    <?php
            endif;
        endforeach;
    endif; ?>

</script>

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="page-header">
                <h1><?php echo _('Utilities - Debug iMilkNET'); ?></h1>
            </div>
        </div>
    </div>
    <!-- /.row -->

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading"><?php echo _('Command Sender'); ?></div>
                <div class="panel-body">
                    <div class="row">
                        <?php
                            $attributes = array('class' => 'form-horizontal', 'role' => 'form');
                            echo form_open('utilities/sendcmd', $attributes);
                        ?>
                        <div class="col-lg-6">

                            <?php if(isset($msg) && $msg) { ?>
                            <div class="alert <?php echo $msg_class; ?> alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="<?php echo _("Close"); ?>"><span aria-hidden="true">&times;</span></button><?php $lang_version = $msg;
                            echo _("{$lang_version}");?></div>
                            <?php } ?>

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
                                <label for="CMD" class="col-sm-2 control-label"><?php echo _('CMD'); ?> *</label>
                                <div class="col-sm-9">
                                    <input type="textdomain" class="form-control" id="CMD" name="CMD" placeholder="<?php echo _('Enter'); ?> CMD" value="<?php echo set_value('CMD'); ?>">
                                    <?php echo form_error('CMD','<span class="label label-danger">','</span>'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="STR" class="col-sm-2 control-label"><?php echo _('STR'); ?></label>
                                <div class="col-sm-9">
                                    <input type="textdomain" class="form-control" id="STR" name="STR" placeholder="<?php echo _('Enter'); ?> STR" value="<?php echo set_value('STR'); ?>">
                                    <?php echo form_error('STR','<span class="label label-danger">','</span>'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-2 col-sm-offset-2">
                                    <button type="submit" name="submit" class="btn btn-success btn-lg"><?php echo _('SEND'); ?></button>
                                </div>
                            </div>

                        </div>

                    </form>
                </div>
            </div>
        </div>

        <?php if (isset($post_data) && $post_data) { ?>
        <div class="panel panel-default panel-debug">
            <div class="panel-heading"><?php echo _('Command Sent at').' '.display_datetime((int) $time_send, true).' '.system_time_format((int) $time_send); ?></div>
            <div class="panel-body">
                <h4>WS Endpoint</h4>
                <dl class="dl-horizontal">
                    <dt>URL</dt>
                    <dd><?php echo $post_url; ?></dd>
                </dl>
                <h4><?php echo _('POST'); ?></h4>
                <dl class="dl-horizontal">
                    <dt><?php echo _('CMD'); ?></dt>
                    <dd><?php echo $post_data['CMD']; ?></dd>

                    <?php if (isset($post_data['STR']) && $post_data['STR']) { ?>
                    <dt><?php echo _('STR'); ?></dt>
                    <dd><?php echo $post_data['STR']; ?></dd>
                    <?php } ?>
              </dl>
              <?php $time_diff = $time_receive - $time_send; ?>
              <h4><?php echo _('Response').' <small>('. sprintf('%.4f', $time_diff).' Sec)</small>'; ?></h4>
              <pre class="cmd_pre"><?php print_r($result); ?></pre>
          </div>
        </div>
        <?php } ?>
    </div>
</div>

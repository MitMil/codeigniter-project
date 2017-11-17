<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <?php
            if (isset($form_insert) && ($form_insert)) {
                $page_name_postfix = _('Insert');
            } else {
                $page_name_postfix = _('Update');
            }
            $page_name_postfix = ($page_name_postfix) ? ' | '.$page_name_postfix : '' ;
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
                        <?php
                        $attributes = array('class' => 'form-horizontal', 'role' => 'form');
                        if (isset($update) && $update) {
                            echo form_open('roles/insert?id='.$role_id, $attributes);
                        } else {
                            echo form_open('roles/insert', $attributes);
                        }
                        ?>

                            <div class="col-lg-7 col-md-offset-1">
                                <?php if (isset($msg) && $msg) { ?>
                                    <div class="alert <?php echo $msg_class; ?> alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="<?php echo _("Close"); ?>"><span aria-hidden="true">&times;</span></button>
                                    <?php
                                    $lang_version = $msg;
                                    echo _("{$lang_version}");?></div>
                                <?php } ?>

                                <div class="form-group">
                                    <label for="name" class="col-sm-3 control-label"><?php echo _('Name'); ?> *</label>
                                    <div class="col-sm-9">
                                        <input type="textdomain" class="form-control" id="name" name="name" placeholder="<?php echo _('Enter'); ?> <?php echo _('Name'); ?>" value="<?php echo (isset($update) && $update) ? $role_data['name'] : set_value('name'); ?>">
                                        <?php echo form_error('name', '<span class="label label-danger">', '</span>'); ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-3 col-sm-offset-3">
                                    <?php if (isset($update) && $update) { ?>
                                        <input type="hidden" name="update" value="<?php echo $role_data['role_id']; ?>">
                                    <?php } ?>
                                        <button type="submit" name="submit" class="btn btn-success btn-lg"><?php echo _('Save'); ?></button>
                                    </div>
                                </div>

                            </div><!-- /.col-lg-6 -->
                            <div class="col-lg-4">
                                <?php echo validation_errors('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="'._("Close").'"><span aria-hidden="true">&times;</span></button>', '</div>'); ?>
                            </div><!-- /.col-lg-6 -->

                        </form>

                    </div><!-- /.row (nested) -->

                </div><!-- /.panel-body -->

            </div><!-- /.panel -->

        </div><!-- /.col-lg-12 -->

    </div><!-- /.row -->
</div>
<!-- /#page-wrapper -->

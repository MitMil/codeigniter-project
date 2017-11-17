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

                        <?php
                        $attributes = array('class' => 'form-horizontal', 'role' => 'form');
                            echo $error="";
                        if (isset($update) && $update) {
                            echo form_open_multipart('users/insert?id='.$user_id, $attributes);
                        } else {
                            echo form_open_multipart('users/insert', $attributes);
                        }
                        ?>

                        <div class="col-lg-7 col-md-offset-1">
                            <?php if (isset($msg) && $msg) { ?>
                                <div class="alert <?php echo $msg_class; ?> alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="<?php echo _("Close"); ?>"><span aria-hidden="true">&times;</span></button>
                                <?php $lang_version = $msg;
                                echo _("{$lang_version}");?></div>
                            <?php } ?>
                            <div class="form-group">
                                <label for="username" class="col-sm-3 control-label"><?php echo _('Username'); ?> *</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="username" name="username" placeholder="<?php echo _('Enter Username'); ?>" value="<?php echo (isset($update) && $update) ? $user_data['username'] : set_value('username'); ?>">
                                    <?php echo form_error('username', '<span class="label label-danger">', '</span>'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="roles" class="col-sm-3 control-label"><?php echo _('Role'); ?> *</label>
                                <div class="col-sm-9">
                                    <div>
                                    <?php
                                    if (isset($roles) && $roles) {

                                        $assign_roles_ids_arr = array();
                                        if (isset($update) && $update && $user_data['roles']) {
                                            foreach ($user_data['roles'] as $key => $value) {
                                                $assign_roles_ids_arr[$value['role_id']] = $value['role_id'];
                                            }
                                        }

                                        $role_parent = array();
                                        foreach ($roles as $key => $role) {
                                            if ($role['parent']) {
                                                $role_parent[] = $role;
                                                continue;
                                            }
                                            $checked = '';
                                            if (isset($update) && $update) {
                                                if (($assign_roles_ids_arr) && array_key_exists($role['role_id'], $assign_roles_ids_arr)) {
                                                    unset($assign_roles_ids_arr[$role['role_id']]);
                                                    $checked ='checked="checked"';
                                                }
                                            } ?>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" id="parent_<?php echo $role['role_id']; ?>" class="parent_roles threads" name="role[]" value="<?php echo $role['role_id']; ?>" <?php echo $checked; ?> /><?php echo $role['name']; ?>
                                                </label>
                                            </div>
                                        <?php
                                        }

                                        if ($role_parent) {
                                            foreach ($role_parent as $key => $role) {
                                                $checked = '';
                                                $show = false;
                                                if (isset($update) && $update) {
                                                    if (($assign_roles_ids_arr) && array_key_exists($role['role_id'], $assign_roles_ids_arr)) {
                                                        $checked ='checked="checked"';
                                                        $show = true;
                                                    }
                                                } ?>
                                                <div class="checkbox check_parent parent_<?php echo $role['parent']; ?>"<?php echo (!$show) ? 'style="display:none;"' : ''; ?>>
                                                    <label>
                                                        <input type="checkbox" class="child child_<?php echo $role['parent']; ?>" name="role[]" value="<?php echo $role['role_id']; ?>" <?php echo $checked; ?> /><?php echo $role['name']; ?>
                                                    </label>
                                                </div>
                                            <?php
                                            }
                                        }
                                    } ?>
                                    </div>
                                    <?php echo form_error('role[]', '<span class="label label-danger">', '</span>'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="name" class="col-sm-3 control-label"><?php echo _('Name'); ?> *</label>
                                <div class="col-sm-9">
                                    <input type="textdomain" class="form-control" id="name" name="name" placeholder="<?php echo _('Enter Name'); ?>" value="<?php echo (isset($update) && $update) ? $user_data['name'] : set_value('name'); ?>">
                                    <?php echo form_error('name', '<span class="label label-danger">', '</span>'); ?>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group">
                                <label for="lastname" class="col-sm-3 control-label"><?php echo _('Password'); ?> *</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="<?php echo _('Enter Password'); ?>" value="<?php echo (isset($update) && $update) ? $user_data['password'] : set_value('password'); ?>" >
                                    <?php echo form_error('password', '<span class="label label-danger">', '</span>'); ?>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group">
                                <label for="email" class="col-sm-3 control-label"><?php echo _('Email'); ?> *</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="<?php echo _('Enter Your Email ID'); ?>" value="<?php echo (isset($update) && $update) ? $user_data['email'] : set_value('email'); ?>" >
                                    <?php echo form_error('email', '<span class="label label-danger">', '</span>'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="address" class="col-sm-3 control-label"><?php echo _('Address'); ?></label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" id="address" name="address" placeholder="<?php echo _('Enter Address'); ?>"><?php echo (isset($update) && $update) ? $user_data['address'] : set_value('address'); ?></textarea>
                                    <?php echo form_error('address', '<span class="label label-danger">', '</span>'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="city" class="col-sm-3 control-label"><?php echo _('City'); ?></label>
                                <div class="col-sm-9">
                                    <input type="textdomain" class="form-control" id="city" city="city" placeholder="<?php echo _('Enter City'); ?>" value="<?php echo (isset($update) && $update) ? $user_data['city'] : set_value('city'); ?>">
                                    <?php echo form_error('city', '<span class="label label-danger">', '</span>'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="state" class="col-sm-3 control-label"><?php echo _('State'); ?></label>
                                <div class="col-sm-9">
                                    <input type="textdomain" class="form-control" id="state" state="state" placeholder="<?php echo _('Enter state'); ?>" value="<?php echo (isset($update) && $update) ? $user_data['state'] : set_value('state'); ?>">
                                    <?php echo form_error('state', '<span class="label label-danger">', '</span>'); ?>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group">
                                <label for="cap" class="col-sm-3 control-label"><?php echo _('CAP'); ?></label>
                                <div class="col-sm-9">
                                    <input type="cap" class="form-control" id="cap" name="cap" placeholder="<?php echo _('Enter CAP'); ?>"  value="<?php echo (isset($update) && $update) ? $user_data['cap'] : set_value('cap'); ?>" >
                                    <?php echo form_error('cap', '<span class="label label-danger">', '</span>'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="col-sm-3 control-label"><?php echo _('Phone'); ?></label>
                                <div class="col-sm-9">
                                    <input type="phone" class="form-control" id="phone" name="phone" placeholder="<?php echo _('Enter Phone'); ?>" value="<?php echo (isset($update) && $update) ? $user_data['phone'] : set_value('phone'); ?>" >
                                    <?php echo form_error('phone', '<span class="label label-danger">', '</span>'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="language" class="col-sm-3 control-label"><?php echo _('Language'); ?></label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="language" name="language">
                                        <?php
                                        if (isset($update) && $update && $user_data['all_languages']) {
                                            if (IS_ADMIN) { ?>
                                                <option value="en_SUI">English (Admin)</option>
                                            <?php
                                            }
                                            foreach ($user_data['all_languages'] as $key => $value) {
                                                ?>
                                                <option id="<?php echo $value['idlang']; ?>" value="<?php echo $value['idlang']; ?>" <?php echo ($user_data['language'] == $value['idlang'] || $user_data['language'] == $value['lang']) ? 'selected="selected"' : '';
                                                ?>>
                                                <?php echo ($value['lang']) ? $value['lang'] : $value['code']; ?>
                                                </option>
                                                <?php
                                            }
                                        } ?>
                                    </select>
                                    <?php echo form_error('language', '<span class="label label-danger">', '</span>'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="date_format" class="col-sm-3 control-label"><?php echo _('Date Format'); ?></label>
                                <div class="col-sm-9">
                                    <?php
                                    $dateformat_choices = dateformat_choices();
                                    $date_format = '';
                                    if (isset($update) && $update) {
                                        $date_format = $user_data['date_format'];
                                    }
                                    ?>
                                    <select class="form-control" id="date_format" name="date_format">
                                    <?php
                                    if ($dateformat_choices) {
                                        foreach ($dateformat_choices as $value => $display) {
                                            $selected = (isset($update) && $date_format == $value) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $value; ?>" <?php echo $selected; ?> > <?php echo $display; ?> </option>
                                    <?php
                                        }
                                    }
                                    ?>
                                    </select>
                                    <?php echo form_error('date_format', '<span class="label label-danger">', '</span>'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="date_format" class="col-sm-3 control-label"><?php echo _('Time Format'); ?></label>
                                <div class="col-sm-9">
                                    <?php
                                    $time_format = '';
                                    if (isset($update) && $update) {
                                        $time_format = $user_data['time_format'];
                                    }
                                    ?>
                                    <select class="form-control" id="time_format" name="time_format">
                                        <option value="H:i:s" <?php echo (isset($update) && $time_format == 'H:i:s') ? 'selected' : ''; ?>> 24-<?php echo _('hours'); ?></option>
                                        <option value="h:i:s A" <?php echo (isset($update) && $time_format == 'h:i:s A') ? 'selected' : '';?>> 12-<?php echo _('hours'); ?></option>
                                    </select>
                                    <?php echo form_error('time_format', '<span class="label label-danger">', '</span>'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="calender_type" class="col-sm-3 control-label"><?php echo _('Calender Type'); ?></label>
                                <div class="col-sm-9">
                                    <input type="calender_type" class="form-control" id="calender_type" name="calender_type" placeholder="<?php echo _('Enter Calender Type'); ?>" value="<?php echo (isset($update) && $update) ? $user_data['calender_type'] : set_value('calender_type');  ?>" >
                                    <?php echo form_error('calender_type', '<span class="label label-danger">', '</span>'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="name" class="col-sm-3 control-label"><?php echo _('Profile Image'); ?></label>
                                <div class="col-sm-9">
                                    <input type="file" id="name" name="profile">
                                    <p class="help-block"><?php echo _('1 MB file limit.'); ?></p>
                                    <?php if (isset($update) && $update && $user_data['profile']) { ?>
                                        <input class="image_old" type="hidden" name="old_file" value="<?php echo $user_data['profile']; ?>" >
                                        <?php
                                        $class = '';
                                        if (strpos($user_data['profile'], 'fa fa-') !== false) {
                                            $class = 'hide';
                                        }
                                        ?>
                                        <img class="image_old <?php echo $class; ?>" src="<?php echo base_url().'assets/uploads/'.$user_data['profile']?>" height="100px" width="100px" style="float:left;" >
                                        <div class="btn btn-danger btn-remove btn-xs <?php echo $class; ?>"><i class="fa fa-remove"></i></div>
                                    <?php } ?>
                                    <div id="myImg"></div>
                                    <?php echo form_error('name', '<span class="label label-danger">', '</span>'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-3 col-sm-offset-3">
                                    <?php
                                    if (isset($update) && $update) { ?>
                                        <input type="hidden" name="update" value="<?php echo $user_data['user_id']; ?>">
                                    <?php
                                    } ?>
                                    <input type="hidden" name="selected_icon" value="fa fa-user" >
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

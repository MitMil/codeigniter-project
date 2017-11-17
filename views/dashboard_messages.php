<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="page-header">
                <h1><?php echo _('Dashboard'); ?></h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <?php
            if (isset($_GET['msg']) && $_GET['msg']) {
                $msg = $_GET['msg'];
                $msg_class = $_GET['msg_class'];
            }
            ?>
            <div class="msg_alert alert <?php echo (isset($msg_class) && $msg_class) ? $msg_class : ''; ?> alert-dismissible <?php echo (isset($msg) && $msg) ? $msg : 'hide'; ?>" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="<?php echo _('Close'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
                <?php echo (isset($msg) && $msg) ? $msg : ''; ?>
            </div>
            <?php //if (!IS_MANAGER) { ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <?php echo _('Error'); ?> <?php echo _('Messages'); ?>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                   	<div class="dataTable_wrapper table-responsive">
	                   	<form method="post">
	                        <table class="table table-striped table-bordered table-hover languagedataTable">
		                        <thead>
		                            <tr class="info">
		                                <th><?php echo _('Error'); ?></th>
		                                <th><?php echo _('Date'); ?></th>
		                                <?php echo (IS_ADMIN || IS_SERVICE) ? '<th> Select </th>' : ''; ?>
		                            </tr>
		                        </thead>
		                        <tbody>
		                        <?php
		                        if ($service_messages) {
									foreach ($service_messages as $key => $value) { ?>
		                        		<tr class="odd gradeA" id="tr_<?php echo $value['idmessage']; ?>">
		                                	<td><?php echo ($value['category'] == 'KPI') ? ucfirst(str_replace('_', ' ', $value['descrizione'])) : $value['descrizione']; ?></td>
		                                    <td><?php echo (is_null($value['ts']) || strpos($value['ts'], '0000-00-00') === false) ? $value['ts'] : '-';?></td>
		                                    <?php if (IS_ADMIN || IS_SERVICE) { ?>
		                                    <td><input type="checkbox" name="serivce_checkbox[<?php echo $value['idmsglog']; ?>]" <?php echo ($value['checked'] == 1) ? 'checked=checked' : ''; ?>></input></td>
		                                    <?php } ?>
		                                </tr>
		                        <?php
		                    		}
		                        }
		                        ?>
		                        </tbody>
	                        </table>
	                        <?php if (IS_ADMIN || IS_SERVICE) { ?>
                            <button  type="submit" name="service_submit" class="btn btn-primary btn-md" value="service_submit"><?php echo _('Save Changes'); ?></button>
                            <?php } ?>
                        </form>
                    </div>
                </div>
            </div>
            <?php //} ?>
         <div class="panel panel-primary">
                <div class="panel-heading">
                    <?php echo _('Animal'); ?> <?php echo _('Messages'); ?>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                   	<div class="dataTable_wrapper table-responsive">
	                   	<form method="post">
	                        <table class="table table-striped table-bordered table-hover languagedataTable">
		                        <thead>
		                            <tr class="info">
		                                <th><?php echo _('Animal Number'); ?></th>
		                                <th><?php echo _('Error'); ?></th>
		                                <th><?php echo _('Date'); ?></th>
		                            </tr>
		                        </thead>
		                        <tbody>
		                        <?php
		                        if ($animal_messages) {
									foreach ($animal_messages as $key => $value) { ?>
		                        		<tr class="odd gradeA" id="tr_<?php echo $value['idmessage']; ?>">
		                                	<td><a target="_blank" href="<?php echo getVelosURL($value['idanimal']);?>" rel="noreferrer"><?php echo $value['number_animal']; ?></a></td>
		                                	<td><?php echo str_replace('_', ' ', $value['descrizione']); ?></td>
		                                    <td><?php echo (is_null($value['ts']) || strpos($value['ts'], '0000-00-00') === false) ? $value['ts'] : '-'; ?></td>
		                                </tr>
		                        <?php
		                    		}
		                        }
		                        ?>
		                        </tbody>
	                        </table>
                        </form>
                    </div>
                </div>
            </div>
	</div>
</div>
<style type="text/css">
	table.languagedataTable tbody td img {
	    float: left;
	    margin-right: 15px;
	    width: 30px;
	}
	table.languagedataTable tbody td span {
	    line-height: 30px;
	}
</style>
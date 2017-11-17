<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="page-header">
                <h1><?php echo _('Dashboard'); ?></h1>
            </div>
        </div>
    </div>
    <!-- /.row -->
    <div class="dashboard_page">
	    <div class="content_block">
	    	<div class="row">
	    		<div class="col-md-8">
	    			<div class="table_info table-responsive">
	    				<table class="table">
						    <thead>
						      <tr>
						      	<th></th>
						        <th><img src="<?php echo base_url(). 'assets/images/dashboard/icon/ATTENZIONI_HEAT.png'; ?>" class="img-responsive"></th>
						        <th><img src="<?php echo base_url(). 'assets/images/dashboard/icon/Calendario.png'; ?>" class="img-responsive"/></th>
						        <th><img src="<?php echo base_url(). 'assets/images/milking/icon/Milking.png'; ?>" class="img-responsive"></th>
						        <th><img src="<?php echo base_url(). 'assets/images/dashboard/icon/Feeding.png'; ?>" class="img-responsive"></th>
						        <th><img src="<?php echo base_url(). 'assets/images/dashboard/icon/ATTENZIONI_ILLNESSCODE.png'; ?>" class="img-responsive"></th>
						        <th><img src="<?php echo base_url(). 'assets/images/dashboard/icon/SEPARATION.png'; ?>" class="img-responsive"></th>
						        <th><img src="<?php echo base_url(). 'assets/images/dashboard/icon/MESSAGE_GENERAL.png'; ?>" class="img-responsive"></th>
						      </tr>
						    </thead>
						    <tbody>
								<?php if ($animal_messages) {
									foreach ($animal_messages as $number_animal => $v2) {
											foreach ($v2 as $idanimal => $v) {
										?>
										<tr class="<?php echo $idanimal ?>">
											<td class="<?php echo $idanimal ?>"><a target="_blank" href="<?php echo getVelosURL($idanimal);?>" rel="noreferrer"><?php echo $number_animal; ?></a></td>
											<?php
											$col1 = '';
											if(in_array(2, $v) || in_array(4, $v)) {
												if(in_array(2, $v)) {
													$descrizione = array_search(2, $v);
													$col1 .= "<div class='img-block'><img src=" .base_url()."assets/images/dashboard/icon/ATTENZIONI_CALORE.png class='img-responsive' title='".str_replace('_', ' ', $descrizione)."'></div>";
												?>
												<?php }
												if(in_array(4, $v)) {
													$descrizione = array_search(4, $v);
													$col1 .= "<div class='img-block'><img src=" .base_url()."assets/images/dashboard/icon/ATTENZIONI_CHECKED_CALORE.png class='img-responsive' title='".str_replace('_', ' ', $descrizione)."'></div>";
												?>
												<?php
												}
											}
											?><?php if ($col1) { ?>
												<td class="<?php echo $idanimal ?>"><div class="multi-img">
												<?php echo $col1; ?>
												</div></td>
											<?php
											} else {
												echo '<td><div class="multi-img"><div class="img-block"></div></div></td>';
											}
											$col2 = '';
											if(in_array(304, $v) || in_array(306, $v) || in_array(305, $v) || in_array(303, $v) || in_array(302, $v)|| in_array(301, $v)) {
												if(in_array(305, $v) ) {
													$descrizione = array_search(305, $v);
													$col2 .= "<div class='img-block'><img src=" .base_url()."assets/images/dashboard/icon/CALENDAR_NO_INSEMINATION.png class='img-responsive' title='".str_replace('_', ' ', $descrizione)."'></div>";
												?>
												<?php
												}
												if(in_array(306, $v)) {
													$descrizione = array_search(306, $v);
													$col2 .= "<div class='img-block'><img src=" .base_url()."assets/images/dashboard/icon/CALENDAR_HEAT.png class='img-responsive' title='".str_replace('_', ' ', $descrizione)."'></div>";
												?>
												<?php
												}
												if(in_array(303, $v)) {
													$descrizione = array_search(303, $v);
													$col2 .= "<div class='img-block'><img src=" .base_url()."assets/images/dashboard/icon/CALENDAR_PREGNANCY_CHECK.png class='img-responsive' title='".str_replace('_', ' ', $descrizione)."'></div>";
													?>
												<?php
												}
												if(in_array(304, $v)) {
													$descrizione = array_search(304, $v);
													$col2 .= "<div class='img-block'><img src=" .base_url()."assets/images/dashboard/icon/CALENDAR_NO_HEAT.png class='img-responsive' title='".str_replace('_', ' ', $descrizione)."'></div>";
												?>
												<?php
												}
												if(in_array(302, $v)) {
													$descrizione = array_search(302, $v);
													$col2 .= "<div class='img-block'><img src=" .base_url()."assets/images/dashboard/icon/CALENDAR_DRY_OFF.png class='img-responsive' title='".str_replace('_', ' ', $descrizione)."'></div>";
												?>

												<?php
												}
												if(in_array(301, $v)) {
													$descrizione = array_search(301, $v);
													$col2 .= "<div class='img-block'><img src=" .base_url()."assets/images/dashboard/icon/CALENDAR_CALVING.png class='img-responsive' title='".str_replace('_', ' ', $descrizione)."'></div>";
												?>

												<?php
												}
											}
											?>
											<?php if ($col2) { ?>
												<td class="<?php echo $idanimal ?>"> <div class="multi-img">
												<?php echo $col2; ?>
												</div></td> <?php
											} else {
												echo '<td><div class="multi-img"><div class="img-block"></div></div></td>';
											}
											$col3 = '';
											if(in_array(201, $v) || in_array(202, $v)) {
												if(in_array(201, $v)) {
													$descrizione = array_search(201, $v);
													$col3 .= "<div class='img-block'><img src=" .base_url()."assets/images/dashboard/icon/ATTENZIONI_LATTE.png class='img-responsive' title='".str_replace('_', ' ', $descrizione)."'></div>";
											?>
											<?php } if(in_array(202, $v)) {
													$descrizione = array_search(202, $v);
													$col3 .= "<div class='img-block'><img src=" .base_url()."assets/images/dashboard/icon/CALENDAR_DRY_OFF.png class='img-responsive' title='".str_replace('_', ' ', $descrizione)."'></div>";
												?>
											<?php }
											}
											if ($col3) { ?>
												<td class="<?php echo $idanimal ?>"><div class="multi-img">
												<?php echo $col3; ?>
												</div></td> <?php
											} else {
												echo '<td><div class="multi-img"><div class="img-block"></div></div></td>';
											}
											$col4 = '';
											if(in_array(101, $v) || in_array(103, $v)) {
												if(in_array(101, $v)) {
													$descrizione = array_search(101, $v);
													$col4 .= "<div class='img-block'><img src=" .base_url()."assets/images/dashboard/icon/RESIDUAL_FEED.png class='img-responsive' title='".str_replace('_', ' ', $descrizione)."'></div>";
											?>
											<?php } if(in_array(103, $v)) {
												$descrizione = array_search(103, $v);
												$col4 .= "<div class='img-block'><img src=" .base_url()."assets/images/dashboard/icon/RESIDUAL_FEED.png class='img-responsive' title='".str_replace('_', ' ', $descrizione)."'></div>";
											?>
											<?php }
											}
											if ($col4) { ?>
												<td class="<?php echo $idanimal ?>"><div class="multi-img">
												<?php echo $col4; ?>
												</div></td> <?php
											} else {
												echo '<td><div class="multi-img"><div class="img-block"></div></div></td>';
											}
											if(in_array(700, $v) || in_array(702, $v)) {
												$descrizione = array_search(702, $v);
											?>
											<td class="<?php echo $idanimal ?>"><div class="multi-img"><div class='img-block'>
												<img src="<?php echo base_url(). 'assets/images/dashboard/icon/ATTENZIONI_ILLNESSCODE.png'; ?>" class="img-responsive" title="<?php echo str_replace('_', ' ', $descrizione); ?>"></div></div>
											</td>
											<?php
											} else {
												echo '<td><div class="multi-img"><div class="img-block"></div></div></td>';
											}
											if(in_array(0, $v)) {
												$descrizione = array_search(0, $v);
											?>
											<td class="<?php echo $idanimal ?>"><div class="multi-img"><div class='img-block'>
												<img src="<?php echo base_url(). 'assets/images/dashboard/icon/SEPARATION.png'; ?>" class="img-responsive" title="<?php echo str_replace('_', ' ', $descrizione); ?>"></div></div>
											</td>
											<?php
											} else {
												echo '<td><div class="multi-img"><div class="img-block"></div></div></td>';
											}
											if(in_array(604, $v)) {
												$descrizione = array_search(604, $v);
											?>
											<td class="<?php echo $idanimal ?>"><div class="multi-img"><div class='img-block'>
												<img src="<?php echo base_url(). 'assets/images/dashboard/icon/SMART_TAG_ERROR.png'; ?>" class="img-responsive" title="<?php echo str_replace('_', ' ', $descrizione); ?>"></div></div>
											</td>
											<?php
											} else {
												echo '<td><div class="multi-img"><div class="img-block"></div></div></td>';
											}
											?>
										</tr> 
								<?php }
									}
								}
								?>
						    </tbody>
						</table>
	    			</div>
	    		</div>
	    		<div class="col-md-4">
                	<?php
                    if ($service_messages) { ?>
	    			<div class="sidebar_block">
	    				<div class="content_info">
	    					<div class="panel panel-default">
	                        <div class="panel-heading">
	                            <div class="title_block"><?php echo ' '._('Error Messages') ?></div>
	                            <div class="right_block"><a href="<?php echo base_url().'dashboard/dashboard_messages'; ?>" class="view_all" ><small><?php echo _('View All') ?></small></a></div>
	                        </div>
	                        <!-- /.panel-heading -->
	                        <div class="panel-body">
	                            <div class="list-group">
										<?php
										foreach ($service_messages as $key => $value) {
											if ($value['checked'] != 1) {
											?>
				                                <a href="#?" class="list-group-item messages"> <?php if ($value['category'] == 'KPI') { ?>
				                                	<span><img src="<?php echo base_url(). 'assets/images/dashboard/icon/KPI.png'; ?>" class="img-responsive"></span> <?php echo ucfirst(str_replace('_', ' ', $value['descrizione'])).' ('.$value['value'].')'; } else { ?> <span><img src="<?php echo base_url(). 'assets/images/dashboard/icon/Service.png'; ?>" class="img-responsive"></span> <?php echo $value['descrizione'].' ('.$value['m_count'].')'; }?>  
				                                <input type="hidden" class="hidden_time" id="<?php echo $key ?>" value="<?php echo $value['ts']; ?>"></input>
				                                </a>
				                        <?php
				                    		}
			                    		}
			                    	?>
	                            </div>
	                            <!-- /.list-group -->
	                        </div>
	                        <!-- /.panel-body -->
	                    </div>
	    				</div>
	    			</div>
			        <?php } ?>
	    			<div class="sidebar_block">
	    				<div class="content_info">
	    					<div class="panel panel-default">
	                        <div class="panel-heading">
	                            <div class="title_block"><?php echo _('Information Panel'); ?></div>
	                            <div class="right_block"><a href="#" class="view_all" data-toggle="modal" data-target="#myModal2"><small><?php echo _('Set your Info') ?></small></a></div>
	                        </div>
	                        <!-- /.panel-heading -->
	                         <div class="panel-body">
	                            <div class="list-group">
	                            	<?php
			                        if ($information_messages) {
			                        	$flag = false;
										foreach ($information_messages as $key => $value) {
												$flag = true;
												$value['calc_data'] = ($value['calc_data']) ? $value['calc_data'] : '0';
											?>
												<a href="#?" class="list-group-item">
				                                    <?php echo $value['descrizione'] .' : '. $value['calc_data'].' '.$value['udm']; ?>
				                                </a>
			                        <?php
			                    		}
			                    		if (!$flag) {
			                    			$_information_messages = array_slice($information_messages, 0, 4);
												foreach ($_information_messages as $key => $value) {
													$value['calc_data'] = ($value['calc_data']) ? $value['calc_data'] : '0';
													?>
													<a href="#?" class="list-group-item">
					                                    <?php echo $value['descrizione'] .' : '. $value['calc_data'].' '.$value['udm']; ?>
					                                </a>
					                        	<?php
					                    		}
			                    			}
			                        	}
			                        ?>
	                            </div>
	                            <!-- /.list-group -->
	                        </div>
	                        <!-- /.panel-body -->
	                    </div>
	    				</div>
	    			</div>
	    		</div>
	    	</div>
	    </div>
	    <?php 
	    	/**
	    	 * Widget Block
	    	 * 1) widget- KPI
	    	 * @todo Optimize KPI Widget Code
	    	 */
	    ?>
	    <div class="widget_block">
	    	<!-- <div class="row"> -->
	    		<!-- <div class="col-md-12"> -->
	    		 <div id="kpi_graphs_wrapper">
		            <div class="col-lg-12 col-sm-12 left_block">
		                <div class="row">
		               		<?php if ($has_widgets_data) {
		               			foreach ($has_widgets_data as $key => $value) {
		               				if ($value['class_name'] === 'expected_calving_interval_dashboard') { ?>
		               					<div class="col-lg-3 col-sm-3" id="expected_calving_interval_dashboard">
							                <?php
							                $graph_id     = 'expected_calving_interval_dashboard';
							                $graph_name   = _('Expected Calving Interval');
							                $graph_value  = $expected_calving_interval['value'];
							                $graph_colors = BBWEB_EXP_CALVING_INTERVAL;
							                include 'kpi/block/graph_gauge.php';
							                ?>
							            </div>
		               				<?php }
		               				else if ($value['class_name'] === 'first_service_avg_days') { ?>
		               					<div class="col-lg-3 col-sm-3" id="first_service_avg_days">
					                        <?php
					                        $graph_id     = 'first_service_avg_days';
					                        $graph_name   = _('Average Days to First Service');
					                        $graph_value  = $average_days_first_service['value'];
					                        $graph_colors = BBWEB_AVERAGE_DAYS_TO_FS;
					                        include 'kpi/block/graph_gauge.php';
					                        ?>
					                    </div>
		               				<?php }
		               				else if ($value['class_name'] === 'fertility_avg_pregnancy_meter') { ?>
		               					<div class="col-lg-3 col-sm-3" id="fertility_avg_pregnancy_meter">
					                        <?php
					                        $graph_id     = 'fertility_avg_pregnancy_meter';
					                        $graph_name   = _('Average Services per Pregnancy');
					                        $graph_value  = $average_services_per_pregnancy['value'];
					                        $graph_colors = BBWEB_AVG_SERVICES_X_PREGN;
					                        include 'kpi/block/graph_gauge.php';
					                        ?>
					                    </div>
		               				<?php }
		               				else if ($value['class_name'] === 'avg_days_open') { ?>
		               					<div class="col-lg-3 col-sm-3" id="avg_days_open">
					                        <?php
					                        $graph_id     = 'avg_days_open';
					                        $graph_name   = _('Average Days Open');
					                        $graph_value  = $average_days_open['value'];
					                        $graph_colors = BBWEB_AVERAGE_DAYS_OPEN;
					                        include 'kpi/block/graph_gauge.php';
					                        ?>
					                    </div>
		               				<?php }
		               				else if ($value['class_name'] === 'heat_detection_rate') { ?>
		               					<div class="col-lg-3 col-sm-3" id="heat_detection_rate">
					                        <?php
					                        $graph_id     = 'heat_detection_rate';
					                        $graph_name   = _('Heat detection Rate');
					                        $graph_value  = $heat_detection_rate['value']."%";
					                        $graph_colors = BBWEB_HEAT_DETECTION_RATE;
					                        include 'kpi/block/graph_gauge.php';
					                        ?>
					                    </div>
		               				<?php }
		               				else if ($value['class_name'] === 'conception_rate') { ?>
		               					<div class="col-lg-3 col-sm-3" id="conception_rate">
					                        <?php
					                        $graph_id     = 'conception_rate';
					                        $graph_name   = _('Conception Rate');
					                        $graph_value  = $conception_rate['value']."%";
					                        $graph_colors = BBWEB_CONCEPTION_RATE;
					                        include 'kpi/block/graph_gauge.php';
					                        ?>
					                    </div>
		               				<?php }
		               				else if ($value['class_name'] === 'pregnancy_rate') { ?>
		               					<div class="col-lg-3 col-sm-3" id="pregnancy_rate">
					                        <?php
					                        $graph_id     = 'pregnancy_rate';
					                        $graph_name   = _('Pregnancy Rate');
					                        $graph_value  = $pregnancy_rate['value']."%";
					                        $graph_colors = BBWEB_PREGNANCY_RATE;
					                        include 'kpi/block/graph_gauge.php';
					                        ?>
					                    </div>
		               				<?php } else if($value['class_name'] === 'url') { ?>
		               				<div class="bookmark_link">
		               					<i class="fa fa-bookmark" aria-hidden="true"></i>
		               					<?php  //$url_arr = explode(':', $value['custom_value_1']);?>
		               					<?php //if($url_arr && $url_arr[0]) {?>
		               					<span>
		               						<b>
		               							<a href="<?php echo $value['custom_value_2']; ?>" target='_blank' class="" id="custom_url"><?php echo $value['custom_value_1']; ?>
		               							</a>
		               						</b>
		               					</span>
		               					<?php //} ?>
		                                 <a data-toggle="modal" data-target="#confirm-delete" href="#" data-href="<?php echo $value['custom_value_1']; ?>" class="btn btn-danger btn-sm"><i class="fa fa-remove"></i></a>
				                    </div>
		                    <?php }
		               			}
		               		?>

		               		<?php } else { ?>
				            <div class="col-lg-3 col-sm-3" id="expected_calving_interval_dashboard">
				                <?php
				                $graph_id     = 'expected_calving_interval_dashboard';
				                $graph_name   = _('Expected Calving Interval');
				                $graph_value  = $expected_calving_interval['value'];
				                $graph_colors = BBWEB_EXP_CALVING_INTERVAL;
				                include 'kpi/block/graph_gauge.php';
				                ?>
				            </div>		                    
		                    <?php } ?>
		                    <div class="col-lg-4 col-sm-4 right_block">
				                <div class="add_more_block">
				            		<a href="#?" id="add_widgets"><img src="<?php echo base_url(). 'assets/images/plus-button.png'; ?>" class="img-responsive"></a>
				            	</div>
		            		</div>
		                </div>
		            </div>
	        	</div>
	    		<!-- </div> -->
	    	<!-- </div> -->
	    </div>
    </div>
</div>
<!-- model -->
    <div class="modal fade dashboard_modal" id="myModal1" role="dialog">
	    <div class="modal-dialog">
	      <!-- Modal content-->
	      <div class="modal-content">
	        <div class="modal-header">
	          <button type="button" class="close" data-dismiss="modal">&times;</button>
	          <h4 class="modal-title"><?php echo _('Error Messages'); ?></h4>
	        </div>
	        <div class="modal-body">
    		</div>
	        </div>
	    </div>
	    </div>
  	</div>

  	<div class="modal fade dashboard_modal" id="myModal2" role="dialog">
	    <form method="post">
		    <div class="modal-dialog">
		      <!-- Modal content-->
		      <div class="modal-content">
		        <div class="modal-header">
		          <button type="button" class="close" data-dismiss="modal">&times;</button>
		          <h4 class="modal-title"><?php echo _('Information Panel'); ?></h4>
		        </div>
		        <div class="modal-body">
			        	<div class="table_info table-responsive">
		    				<table class="table">
							    <thead>
							      <tr>
							      	<th width="50%"><?php echo _('Information') ?></th>
							        <th width="10%"><?php echo _('Select') ?></th>
							        <th width="15%"><?php echo _('Hours') ?></th>
							        <th width="25%"><?php echo _('Groups') ?></th>
							      </tr>
							    </thead>
							    <tbody>
							    <?php
			                    if ($all_information_messages) {
									foreach ($all_information_messages as $key => $value) {
										$value['calc_data'] = ($value['calc_data']) ? $value['calc_data'] : '0';
										?>
								    	<tr>
								        	<td><?php echo $value['descrizione'] .' : '. $value['calc_data'].' '.$value['udm']; ?></td>
									        <td><input type="checkbox" value="" name="info_checkbox[<?php echo $value['idinfo']; ?>]" <?php echo ($value['checked'] == 1) ? 'checked=checked' : ''; ?>/></td>
									        <td><?php if ($value['class_name'] == 'milking') { ?> 
		                        				<input type="number" min=0 class="form-control" value="<?php echo $value['custom_value_1']; ?>" id="info_hours[<?php echo $value['idinfo']; ?>]" name="info_hours[<?php echo $value['idinfo']; ?>]"></input></td>
	                        				<td><?php if ($value['idinfo'] == 2) { ?>
			                        				<select name="info_groups[<?php echo $value['idinfo']; ?>]" class="form-control" id="">
					                                <?php
					                                foreach ($groups as $k_group => $v_group) {
					                                	$selected = ($v_group['idgroup'] == $value['custom_value_1']) ? "selected=selected": '' ;
					                                    echo '<option value="'.$v_group['idgroup'].'"'.$selected.' >'.$v_group['name'].'</option>';
					                                }
					                                ?>
					                                </select>
		                        				 <?php } ?>
		                        			</td>
		                        				 <?php } else { echo "<td></td>"; } ?>
								      	</tr>
			                    <?php
			                		}
			                    }?>
							    </tbody>
							</table>
		    			</div>
		        </div>
		        <div class="modal-footer">
			        <button type="submit" class="btn btn-primary" name="info_save" value="info_save"><?php echo _('Apply Changes') ?></button>
			    </div>
		      </div>
		    </div>
	    </form>
  	</div>

  	<!-- model -->
    <div class="modal fade dashboard_modal" id="myModal3" role="dialog">
	    <div class="modal-dialog">
	      <!-- Modal content-->
	      <div class="modal-content">
	        <div class="modal-header">
	          <button type="button" class="close" data-dismiss="modal">&times;</button>
	          <h4 class="modal-title"><?php echo _('Select Widgets'); ?></h4>
	        </div>
	        <div class="modal-body">
	        	<div class="table_info table-responsive">
    				<table class="table">
					    <thead>
					      <tr>
					      	<th><?php echo _('KPI') ?></th>
					        <th><?php echo _('Select') ?></th>
					      </tr>
					    </thead>
					    <tbody>
					      <tr>
					        <td><?php echo _('Expected Calving Interval'); ?></td>
					        <td><input type="checkbox" name="id_widget[1]" value="1" class="expected_calving_interval_dashboard widgets_checked" /></td>
					      </tr>
					      <tr>
					        <td><?php echo _('Average Days to First Service'); ?></td>
					        <td><input type="checkbox" name="id_widget[2]" value="2" class="first_service_avg_days widgets_checked"/></td>
					      </tr>
					      <tr>
					        <td><?php echo _('Average Services per Pregnancy'); ?></td>
					        <td><input type="checkbox" name="id_widget[3]" value="3" class="fertility_avg_pregnancy_meter widgets_checked"/></td>
					      </tr>
					      <tr>
					        <td><?php echo _('Average Days Open'); ?></td>
					        <td><input type="checkbox" name="id_widget[4]" value="4" class="avg_days_open widgets_checked"/></td>
					      </tr>
					      <tr>
					        <td><?php echo _('Heat detection Rate'); ?></td>
					        <td><input type="checkbox" name="id_widget[5]" value="5" class="heat_detection_rate widgets_checked"/></td>
					      </tr>
					      <tr>
					        <td><?php echo _('Conception Rate'); ?></td>
					        <td><input type="checkbox" name="id_widget[6]" value="6" class="conception_rate widgets_checked"/></td>
					      </tr>
					      <tr>
					        <td><?php echo _('Pregnancy Rate'); ?></td>
					        <td><input type="checkbox" name="id_widget[7]" value="7" class="pregnancy_rate widgets_checked" /></td>
					      </tr>
					    </tbody>
					</table>
					<div class="col-lg-12 col-sm-12 bookmark_class">
						<div class="">
							<label style="float: left;margin-right: 15px;margin-top: 6px;"><?php echo _('Enter Link Name : '); ?></label>
	                        <input type="text" class="form-control" id="custom_link_name" style="width: 50%;"></input>
	                        <br>
	                        <label style="float: left;margin-right: 57px;margin-top: 6px;"><?php echo _('Enter URL : '); ?></label>
	                        <input type="text" class="form-control" id="custom_link_input" style="width: 50%;"></input>
                        </div>
                    </div>
    			</div>
	        </div>
	        <div class="modal-footer">
		        <button type="button" class="btn btn-primary" data-dismiss="modal" id="save"><?php echo _('Apply Changes') ?></button>
		    </div>
	      </div>
	    </div>
  	</div>
    <!--  end model -->
<!-- /#page-wrapper -->
<style type="text/css">
	.navbar-static-top, .modal-backdrop {
    	z-index: 1;
	}
	.widget_block div#kpi_graphs_wrapper .panel {
	    margin-top: 20px;
	}
	.bookmark_link .btn-sm {
	    padding: 3px 3px;
	    font-size: 10px;
	    line-height: 0.5;
	    border-radius: 3px;
	    float: right;
	}
	.bookmark_link {
		color: #337ab7;
	}
	.bookmark_class {
	    border: 1px solid #333;
	    padding: 10px 10px;
	    margin-bottom: 20px;
	}
	.widget_block .panel .panel-body {
		padding: 15px;
	}
	.dashboard_page .table_info .table>tbody>tr>td .img-block {
		padding-bottom: 5px;
	}
	.dashboard_page .table_info .table>thead>tr>th {
		text-align: center;
	}
	.dashboard_page .table_info.table-responsive {
	    min-height: .01%;
	    overflow-x: auto;
	    height: 468px;
	    margin-bottom: 20px;
	}
	.multi-img {
		display: grid;
    	grid: repeat(2, 20px) / auto-flow 20px;
	}
	.multi-img .img-block {
		width: 25px;
    	height: 25px;
	}

</style>
<script type="text/javascript">
$(document).on('click', '.messages', function() {
	var html = $(this).text();
	var time = $('#'+$(this).index()).val();
    $('#myModal1 .modal-body').html('<p>Error :  <label>' + html + '</label></p><p>Default View : On</p><p>Last Updated : '+time+'</p>');
    $('#myModal1').modal('show');
});
$(document).on('click', '#add_widgets', function() {
    $('#myModal3').modal('show');
});
$("#save").on("click", function(e){
	var checkedValues = $('.widgets_checked:checked').map(function() {
	    return this.value;
	}).get();
	var uncheckedValues = $('input:checkbox:not(:checked)').map(function() {
	    return this.value;
	}).get();
	Save_Widget(checkedValues, uncheckedValues);
});
function Save_Widget(checkedValues, uncheckedValues) {
	var textBox_url =  $.trim( $('#custom_link_input').val() )
	var textBox_name =  $.trim( $('#custom_link_name').val() )
    $.ajax({
        url: "<?php echo site_url('dashboard/save_widget'); ?>",
        async: true,
        type: "POST",
        data: {
            checkedvalues:checkedValues,
            uncheckedvalues:uncheckedValues,
            url:textBox_url,
            url_name:textBox_name
        },
        dataType: "json",
        success: function(data) {
            if(data) {
            	var html = '<div class="msg_alert alert alert-dismissible '+data.msg_class+'" role="alert">'+data.msg+'<button type="button" class="close" data-dismiss="alert" aria-label="Close">\
                        <span aria-hidden="true">&times;</span></button></div>';
            	$(html).insertAfter('.content_block');
            	location.reload();
            } else {

            }
        }
    });
}
$(document).ready(function(){
	if($('#expected_calving_interval_dashboard').is(":visible"))
	{
	 	$('.expected_calving_interval_dashboard').attr('checked','checked');
	}
	if($('#first_service_avg_days').is(":visible"))
	{
	 	$('.first_service_avg_days').attr('checked','checked');
	}
	if($('#fertility_avg_pregnancy_meter').is(":visible"))
	{
	 	$('.fertility_avg_pregnancy_meter').attr('checked','checked');
	}
	if($('#avg_days_open').is(":visible"))
	{
	 	$('.avg_days_open').attr('checked','checked');
	}
	if($('#heat_detection_rate').is(":visible"))
	{
	 	$('.heat_detection_rate').attr('checked','checked');
	}
	if($('#conception_rate').is(":visible"))
	{
	 	$('.conception_rate').attr('checked','checked');
	}
	if($('#pregnancy_rate').is(":visible"))
	{
	 	$('.pregnancy_rate').attr('checked','checked');
	}
});
$(document).ready(function(){
	$( ".bookmark_link" ).wrapAll( "<div class='col-md-3'><div class='panel panel-primary'><div class='panel-body'></div></div></div>");
	$('.widget_block div#kpi_graphs_wrapper .panel').prepend('<div class="panel-heading"><?php echo _('BookMarks'); ?></div>')
});
</script>
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
                <a href="#" class="btn btn-danger danger btn_bookmark_confirm"><?php echo _('Delete'); ?></a>
            </div>
        </div>
    </div>
</div>

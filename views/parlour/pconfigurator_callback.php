<script>
var salaConfig = <?php echo $data;?>
</script>
<?php
$head_txt = [_('IP'),_('Parlour Type'),_('Left'),_('Right'),_('Action')];
?>

<div id="json"></div>
<div id="notification"></div>
<div id="container">

    <h3><a href="/settings/pconfigurator"><?php echo _('Configurator Wizard'); ?></a></h3>

    <table id="sala-configurator-table" cellspacing="1" cellpadding="0">
		<thead>
		<tr>
			<th id='head_ip' style=""><?php echo $head_txt[0]; ?></th>
			<th id='head_room' style=""><?php echo $head_txt[1]; ?></th>
			<th id='head_left' style=""><?php echo $head_txt[2]; ?></th>
			<th id='head_right' style=""><?php echo $head_txt[3]; ?></th>
			<th id='head_action' colspan="2" style=""><?php echo $head_txt[4]; ?></th>
		</tr>
		</thead>
		<tbody id="devices"></tbody>
	</table>	
</div>


<?php
//}
?>
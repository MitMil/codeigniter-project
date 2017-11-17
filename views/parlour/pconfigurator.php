<script>
var salaConfig = <?php echo $data; ?>;
</script>

<?php
$icons_sala =  APPLICATION_BASE_URL.'application/views/parlour/icons/sala/';
?>

<div id="container">
	<form id="form-sala-configurator" action="" method="post">
		<table id="sala-configurator-table" border=0>
			<thead>
			<tr>
				<th style=""></th>
				<th style=""></th>
				<th style=""></th>
			</tr>
			</thead>
			<tbody>
			<tr id="row-0">
				<td id="1" style=""></td>
				<td id="2" style=""></td>
				<td id="3-0" class="row-td" style=""></td>
			</tr>
			</tbody>
		</table>
		<table id="conferma-configurator">
			<tr>
				<td style="width:22%;"></td>
				<td style="width:25%;"></td>
				<td align="right" id="td-submit-data"></td>
			</tr>
		</table>
	</form>
</div>
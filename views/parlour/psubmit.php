


<?php

$data			= $_POST['data_sala'];
$numero_milknet = $_POST['numero_milknet'];


//echo urldecode($data);

echo "<pre>";
var_dump(json_decode(urldecode($data)));
echo "</pre>";

//$sala = $data->Sala;
//echo $sala;


echo "<table class=parlour align=center>";

echo "<caption>System Configurator</caption>";

echo "<tr><td>Server</td><td>IFC</td></tr>";
echo "<tr><td>Numero Milknet</td><td>".$_POST['numero_milknet']."</td></tr>";

echo "<tr><td><ul>";
for($i=0; $i< $numero_milknet; $i++)
{
	//echo "<li>Miklnet ".($i+1)." ".$_POST['milknet-ip-'.$i]."</li>";
}
echo "</ul></td></tr>";

echo "<tr><td>Tipo Sala</td><td id='table_sala'></td></tr>";

echo "</table>";



?>
<script type="text/javascript" src="https://www.interpuls.it/js/jquery.min.js"></script>

<script>
$(function()
{
	var json_data = "<?php echo $data; ?>";
	var num_milknet = "<?php echo $numero_milknet; ?>";
	var data = JSON.parse(decodeURIComponent(json_data));
	console.log(data);
	console.log('length:'+data.Sala.length);
	//console.log(data.Sala[0].ip);
	for(var i=0; i< data.Sala.length; i++)
	{
		//console.log(data.Sala[i].ip);
	}
	$.each(data, function(i,v)
	{
		console.log('i:'+i+' v:'+v);
		//console.log(data.Sala[v].ip);
	});
});

</script>

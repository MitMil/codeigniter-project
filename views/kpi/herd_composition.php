<?php

$count_animals = 0;
$count_inmilk  = 0;

$herd_graph_data_arr = array();
$herd_graph_data_arr['KEEPOPEN']['value']    = 0;
$herd_graph_data_arr['OPEN']['value']        = 0;
$herd_graph_data_arr['INSEMINATED']['value'] = 0;
$herd_graph_data_arr['PREGNANT']['value']    = 0;

$dim_graph_data=array();

$CI  =& get_instance();
$mod = $CI->load->model('animals/mdl_animal', '', true);

foreach ($herd_composition_data as $idanimal => $animal) {

    $animalValues               = $CI->mdl_animal->initbyAnimalArray($animal);
    $DIM                        = $animalValues->getDIM();
    $total_lactation            = $animalValues->getTotalLactation();
    $velos_production_status    = $animalValues->getProductionStatus();
    $velos_reproduction_status  = $animalValues->getReproductionStatus();

    // Skip processing if Reproduction Status is Dry
    if ($velos_reproduction_status == 'DRY') {
        continue;
    }

    // If lactation is greater then 3, consider as 3+
    if ($total_lactation > 3) {
        $total_lactation = 3;
    }

    // If production and reproduction is not DRY, consider animal as 'In Milk'
    if ($velos_production_status != 'DRY' && $velos_reproduction_status != 'DRY') {
        $count_inmilk += 1;
    }

    if ($velos_production_status != 'DRY') {
        $herd_graph_data_arr[$velos_reproduction_status]['dim'][] = $DIM;
    }

    if ($velos_reproduction_status != 'DRY') {
        $dim_graph_data[]=$DIM;
    }

    $herd_graph_data_arr[$velos_reproduction_status]['data'][] = $animal;

    $herd_graph_data_arr[$velos_reproduction_status]['value'] = $herd_graph_data_arr[$velos_reproduction_status]['value'] + 1;

    $count_animals += 1;
}

// Remove status if null values
foreach ($herd_graph_data_arr as $velos_reproduction_status => $value) {
    if (empty($value['value'])) {
        unset($herd_graph_data_arr[$velos_reproduction_status]);
    }
}

//===================START : Herd composition main graph===================================
$Inner_Arc = array();
$Outer_Arc = array();
$pregnant_count = 0;

$main_colors = getGraphColors();
foreach ($herd_graph_data_arr as $velos_reproduction_status => $value) {

    if ($value['value']) {

        if ($value['data']) {
            $normal_count = 0;
            $dry_count = 0;
            foreach ($value['data'] as $data) {

                $animalValues               = $CI->mdl_animal->initbyAnimalArray($data);
                $velos_production_status    = $animalValues->getProductionStatus();
                $velos_reproduction_status  = $animalValues->getReproductionStatus();

                if ($velos_reproduction_status != 'DRY' && $velos_production_status != 'DRY') {
                    $normal_count += 1;
                } else {
                    $dry_count += 1;
                }
            }

            if ($velos_reproduction_status == 'PREGNANT') {
                $pregnant_count = ($normal_count+$dry_count);
            }

            $generate_url = generate_url("kpi/herd_composition");

            if (strpos($generate_url, '?') !== false) {
                $generate_url = $generate_url . '&';
            } else {
                $generate_url = $generate_url . '?';
            }
            $Inner_Arc[] = '{
                    "name": "'.$velos_reproduction_status.'",
                    "count": '.($normal_count+$dry_count).',
                    "text": "'.(int) ((($normal_count+$dry_count)/ $count_animals)*100).'%",
                    "color": "'.$main_colors[$velos_reproduction_status]["MAIN"].'",
                    "url":"'.$generate_url."reprod_status=".$velos_reproduction_status.'"
                }';

            if ($normal_count > 0) {
                $Outer_Arc[] = '{
                        "name": "'.$velos_reproduction_status.'-INMILK",
                        "count": '.($normal_count).',
                        "color": "'.$main_colors[$velos_reproduction_status]["INMILK"].'",
                        "url":"'.$generate_url.'reprod_status='.$velos_reproduction_status.'&prod_status=INMILK"
                    }';
            }

            if ($dry_count > 0) {
                $Outer_Arc[] = '{
                        "name": "'.$velos_reproduction_status.'-DRY",
                        "count": '.($dry_count).',
                        "color": "'.$main_colors[$velos_reproduction_status]["DRY"].'",
                        "url":"'.$generate_url."reprod_status=".$velos_reproduction_status.'&prod_status=DRY"
                    }';
            }
        }
        $categories[$velos_reproduction_status] = $main_colors[$velos_reproduction_status]["MAIN"];
    } else {
        //unset($main_colors[$reprod_animal]);
    }
}
$graph_inner_dim_value = 0;
if ($pregnant_count != 0 && $count_animals != 0) {
    $graph_inner_dim_value = (int)(($pregnant_count/$count_animals)*100).'% ';
}
?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="page-header">
                <h1><?php echo _('Herd Composition'); ?></h1>
            </div>
            <?php include('filter.php'); //add search filter ?>
        </div>

        <?php
        if (is_fitlers_removed()) { ?>
            <div class="col-lg-12">
                <?php include('block/no_data.php'); ?>
            </div>
            <?php
        } else { ?>
        <!-- Herd Composition Main Graph Area -->
        <div class="col-sm-6 main-stage">
            <?php
            $graph_id         = 'herd_composition_main';
            $graph_name       = _('Pregnant');
            $graph_value      = $graph_inner_dim_value;
            $graph_data_inner = $Inner_Arc;
            $graph_data_outer = $Outer_Arc;
            include 'block/graph_donut.php';
            ?>
        <div class="text-center all_cows_button">
            <a href="<?php echo base_url()?>kpi/herd_composition?reprod_status=ALL" class="btn btn-primary" ><?php echo _('All Cows'); ?></a>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="desktop-downlift">
            <?php
            if ($herd_graph_data_arr) {
                $table_status = $herd_graph_data_arr;
                include 'block/table_status.php';
            }
            ?>
        </div>
    </div>

    <!-- 'Average Lactations' & 'Average DIM' Graphs Area -->
    <div class="desktop-uplift">
        <div class="col-md-3 col-sm-3">
            <?php
            $graph_colors = BBWEB_AVERAGE_LACTATIONS;
            $graph_id     = 'hc_average_lactations';
            $graph_name   = _('Average Lactations');
            $graph_value  = number_format($average_lactations, 0, '.', '');
            include 'block/graph_gauge.php';
            ?>
        </div>
        <div class="col-md-3 col-sm-3">
            <?php
            $average_dim = 0;
            if (isset($main_dim)) {
                $average_dim = $main_dim;
            }
            $graph_colors = BBWEB_AVERAGE_DIM;
            $graph_id     = 'hc_average_dim';
            $graph_name   = _('Average DIM');
            $graph_value  = number_format($average_dim, 0, '.', '');
            include 'block/graph_gauge.php';
            ?>
        </div>
    </div>
    <?php
    } ?>
</div>
<!-- /.row -->
</div>
<!-- /#page-wrapper -->
<script type="text/javascript">

var HC_main_max_height;

jQuery(document).ready(function() {
    if ($("#herd_composition_main").css('min-height')) {
        HC_main_max_height = $("#herd_composition_main").css('min-height').replace(/[^-\d\.]/g, '');;
        $(window).resize();
    }
});

$(window).on('resize', function () {

    var window_width = $(window).width();

    var elem_downlift = $(".desktop-downlift");
    var elem_uplift = $(".desktop-uplift");

    var table_height = elem_downlift.height();
    var table_width = elem_downlift.width();
    var graph_width = elem_uplift.children("div").width();

    // Remove whitespace below graph.
    elem_uplift.children("div").children('.guage').height(graph_width);

    // check for height after guage height has been set according to its width.
    var graph_height = elem_uplift.children("div").height();

    var HC_main_width = $("#herd_composition_main").width();
    if (HC_main_width < HC_main_max_height) {
        $("#herd_composition_main").css('min-height', HC_main_width);
        $("#herd_composition_main").height(HC_main_width);
    }

    if (window_width >= 768) {
        // ipad + desktop
        elem_downlift.css({ position: 'absolute', top: graph_height, padding:'0px 30px 0px 0px', width :'100%' });
        elem_downlift.children('.herd_composition_main_well').css('margin', '0px');
        elem_uplift.children("div").removeClass('col-xs-6');

    } else if((window_width >= 480) && (window_width < 768)) {
        // Mobile Landscape Mode.
        elem_downlift.css({ position: 'relative', top: 0, padding:'0px', width :'100%' });
        elem_uplift.children("div").addClass('col-xs-6');

    } else {
        // Mobile Portrait Mode.
        elem_uplift.children("div").removeClass('col-xs-6');
        elem_downlift.css({ position: 'relative', top: 0, padding:'0px', width :'100%' });
    }
});

</script>

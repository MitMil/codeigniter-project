<?php
extract($room);
?>
<table class="sala_p_intera">
    <tr>
        <!-- left part -->
        <td class='colonna' id='colonna-left' style='border:0px solid; width: 50%'>
        <?php
        $start = $top_left;
        $end = $bottom_left;
        if ($top_left > $bottom_left) {
            $start = $bottom_left;
            $end = $top_left;
        }
        for ($id_pannello = $start; $id_pannello <= $end; $id_pannello++) {
            include('pannello_layout.php');
        }
        ?>
        </td>
        <!-- right part -->
        <td class='colonna' id='colonna-right' style='border:0px solid; width: 50%'>
        <?php
        $start = $top_right;
        $end = $bottom_right;
        if ($top_right > $bottom_right) {
            $start = $bottom_right;
            $end = $top_right;
        }
        for ($id_pannello = $top_right; $id_pannello <= $bottom_right; $id_pannello++) {
            include('pannello_layout.php');
        }
        ?>
        </td>
    </tr>
</table>
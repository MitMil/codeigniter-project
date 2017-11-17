<?php
extract($room);
?>
<table class="rotary" style="width: 50%; margin: auto;">
    <tr>
        <td class="colonna" id="colonna-left" style="border:0px solid; width: 50%">
        <?php
        $start = $top_left;
        $end = $top_right;
        if ($top_left > $top_right) {
            $start = $top_right;
            $end = $top_left;
        }
        for ($id_pannello = $start; $id_pannello <= $end; $id_pannello++) {
            include('pannello_layout.php');
        }
        ?>
        </td>
    </tr>
</table>
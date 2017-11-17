<table class="">
    <tr>
        <td id="pannello_<?php echo $id_pannello; ?>" class="pannello"> <!-- Nuovo pannello in riga -->
            <table class="pannello-desc" border="">
                <thead>
                    <tr>
                        <th class="id_pannello" style=""><?php echo $id_pannello; ?></th>
                        <th class="icon_milking_mode" style=""></th>
                        <th class="number_animal" style=""></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="status_lavaggio" style="display: none;">
                        <td colspan="3">
                            <img src="/assets/images/milking/icon/Lavaggio_OK.png" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="milk-data">
                            <table class="milk-data-desc">
                                <tr>
                                    <td class="prod_reale">&nbsp;</td>
                                    <td class="prod_attesa">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="tempo_prod">&nbsp;</td>
                                    <td class="tempo_prod_exp">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="conductivity">&nbsp;</td>
                                    <td class="conductivity_exp">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="temperatura">&nbsp;</td>
                                    <td class="temperatura_exp">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                        <td class="milk-data">
                            <table>
                                <tr>
                                    <td class="icon_1">&nbsp;</td>
                                    <td class="icon_2">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="icon_3">&nbsp;</td>
                                    <td class="icon_4">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="icon_5">&nbsp;</td>
                                    <td class="icon_6">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="pannello_bottom">
                            <table style="width:100%;">
                                <tr>
                                    <td class="velos_rep_status" style="width:20%;"></td>
                                    <td class="lactation" style="width:30%;text-align:center;">&nbsp;</td>
                                    <td class="lactation_days" style="width:50%;" class="milk-data">&nbsp;</td>
                                    <td class="" style="width:100%;">
                                        <!--<img src="/assets/images/milking/icon/Separazione animale/Separazione animale.png"></img> -->
                                     </td>
                                    <td id="<?php echo $id_pannello;?>" class='graph_image' >
                                        <img src="/assets/images/milking/icon/Grafico_mungitura.png" style="display: none;" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table> <!-- chiusura tabella pannello -->

            <!--demo-->
            <div class="graph_data">
                <div id="graph_table<?php echo $id_pannello;?>" class="line-chart">

                </div>
            </div> <!-- chiusura tabella pannello -->
            <script>
            $(document).ready(function () {
                // create Pannello
                Panels.createPannello(<?php echo $id_pannello;?>);
            });
            </script>
        </td> <!-- chiusura sezione pannello -->
    </tr>
</table>
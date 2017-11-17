 <script>

    var last_request_ts = 0;
    var updatePanelWithComplete = '';
    //var tdFont = ['2','1.5','1.4','1.1','1.4','1.1','1.4','1.1'];
    var tdFont = ['27', '20', '18', '16', '16', '14', '16', '14'];

    // PANNELLO STATUS
    var PANEL_STATUS_DIED               = "f";
    var PANEL_STATUS_NON_ATTIVO         = "e";
    var PANEL_STATUS_NON_ATTIVO_STANDBY = "d";
    var PANEL_STATUS_STANDBY            = "0";
    var PANEL_STATUS_TAKEOFF            = "1";
    var PANEL_STATUS_MILKING_AUTO       = "2";
    var PANEL_STATUS_WASHING            = "3";
    var PANEL_STATUS_PROGRAMMING        = "4";
    var PANEL_STATUS_CALIBRATION        = "5";
    var PANEL_STATUS_MILKING_MANUAL     = "6";

    // Panel Colors based on pannello_status
    var panelColors = {
        'f': '#969696',
        'e': '#dfdfdf',
        'd': '#dfdfdf',
        '0': '#ffffff',
        '1': '#ffffff',
        '2': '#46a3ff',
        '3': '#00ebff',
        '4': '#e6e6e6',
        '5': '#e6e6e6',
        '6': '#46a3ff'
    };

    // Panel Icons based on pannello_status
    var panelIcons = {
        'f': '',
        'e': '',
        'd': '',
        '0': 'Status_standby.gif',
        '1': '',
        '2': 'Mungitura_automatica.png',
        '3': 'Lavaggio_rubinetto.png',
        '4': '',
        '5': '',
        '6': 'Mungitura_manuale.png'
    };

    // alarms status as per change color of perticular values
    var alarms_status_fields = {
        'ALARMS_NO_FLOW':          'no_flow', // not exists
        'ALARMS_CONDUCTIVITY':     'conductivity',
        'ALARMS_LOW_PRODUCTION':   'prod_reale',
        'ALARMS_KICK_OFF':         'tempo_prod',
        'ALARMS_TEMPERATURE':      'temperatura'
    };


    // BLOCCO_MUNGITURA constant values
    var BLOCCO_MUNGITURA_COLOSTRO       = 0;
    var BLOCCO_MUNGITURA_MASTITE        = 1;
    var BLOCCO_MUNGITURA_ANTIBIOTICO    = 2;
    var BLOCCO_MUNGITURA_ASCIUTTA       = 3;
    var BLOCCO_MUNGITURA_USER_DEFINED   = 6;
    var BLOCCO_MUNGITURA_ALTRO          = 7;

    // ATTENZIONI constant values
    var ATTENZIONI_CALENDARIO           = 0;
    var ATTENZIONI_LATTE                = 1;
    var ATTENZIONI_CALORE               = 2;
    var ATTENZIONI_TEMPOLUNGO           = 3;
    var ATTENZIONI_ILLNESSCODE          = 4;
    var ATTENZIONI_CHECKED_CALENDARIO   = 16;
    var ATTENZIONI_CHECKED_LATTE        = 17;
    var ATTENZIONI_CHECKED_CALORE       = 18;
    var ATTENZIONI_CHECKED_TEMPOLUNGO   = 19;

    // ANOMALIA constant values
    var SEGNALAZIONE_ANOMALIA_GIA_MUNTA         = 0;
    var SEGNALAZIONE_ANOMALIA_NON_REGISTRATA    = 1;
    var SEGNALAZIONE_ANOMALIA_NON_ESISTE        = 2;
    var SEGNALAZIONE_ANOMALIA_NO_PEDOMETRO      = 3;

    // ALARMS from PANEL
    var ALARMS_NO_FLOW             = 0;
    var ALARMS_CONDUCTIVITY        = 1;
    var ALARMS_LOW_PRODUCTION      = 2;
    var ALARMS_KICK_OFF            = 3;
    var ALARMS_TEMPERATURE         = 4;

    $(function () {

        $('.milk-data .milk-data-desc').each(function (ii, vv) {
            $("icon").css("text-align", "center");
            $(this).find('td').each(function (i, v) {
                $(this).css({'font-size': tdFont[i] + 'px'});
                if (i % 2 == 0) {
                    $(this).css("text-align", "left");
                }
                else {
                    $(this).css("text-align", "left");
                }
            });
        });

        window.setInterval(updatePannello, 3000);
        window.setInterval(calliMilkNET, 5000);

        function calliMilkNET() {

            postData = {
                    pannelli_totali: <?php echo $pannelli_totali; ?>,
                };

            jQuery.ajax({
                type: "POST",
                url: "<?php echo base_url().'api/imilk/milking/updateAllPanelsWithPartial/'; ?>",
                dataType: 'json',
                data: postData,
                success: function (response) {
                    //console.log(response);
                    if (response.result != "OK") {
                        console.log('ERROR response from calliMilkNET: ' + response.message);
                    }
                },
                error: function () {
                    console.log('ERROR calliMilkNET');
                }
            });

        }

        // initial data fill up
        //updatePannello();

        function updatePannello(idpannello = '') {

            getData = {
                    is_ajax: 1,
                    last_ts: last_request_ts,
                };

            if (idpannello) {
                getData.idpannello = idpannello;
            }
            jQuery.ajax({
                type: "GET",
                url: "<?php echo base_url().getClass().'/monitorajax'; ?>",
                dataType: 'json',
                data: getData,
                success: function (response) {
                    //console.log(response);
                    if (response) {

                        last_request_ts = response.last_ts;
                        //console.log(last_request_ts);

                        if (response.data) {
                            jQuery.each(response.data, function(i, pannelloData) {
                                //console.log(pannelloData);
                                // init PANNELLO by array data
                                Panels.initPannelloByArray(pannelloData);
                            });
                        }
                    }
                },
                error: function () {
                    console.log('ERROR updatePannello');
                }
            });

        }

        $(".graph_image").click(function() {
            var idpannello = this.id;
            if($('#graph_table'+idpannello).is(':visible')) {
                Panels.getPannelloById(idpannello).stopLive();
            } else {
                Panels.getPannelloById(idpannello).startLive();
            }
        });

    });

    // Base Converter
    var ConvertBase = function (num) {
        return {
            from : function (baseFrom) {
                return {
                    to : function (baseTo) {
                        return parseInt(num, baseFrom).toString(baseTo);
                    }
                };
            }
        };
    };

    // Class to manage hex/bin values
    function HexToBinClass (hex_value)
    {
        var self = this;
        self.hex_val = hex_value;
        self.bin_val = "";

        self.getBinValue = function () {
            if(self.hex_val.length == 0 || self.hex_val == undefined) {
                self.hex_val = 0;
            }
            self.bin_val = ConvertBase(self.hex_val).from(16).to(2);
            return self.bin_val;
        };

        self.isBitEnabled = function (blocco_type) {
            var string = self.getBinValue()
            var array = string.split('').reverse();
            if( blocco_type in array && array[blocco_type] == "1") {
                return true;
            } else {
                return false;
            }
        };

        self.isSomeBitEnabled = function () {
            var string = self.getBinValue()
            var array = string.split('').reverse();
            for (var i in array)
            {
                if(array[i] == 1) {
                    return true;
                }
            }
        }

    }


    /*
     *  Panels static class
     *  Manage ALL the procedure for Panels
     */
    var Panels = {

        // set idpannello live opened
        idpannello_live: null,

        // list of panels
        panels: {},

        getPannelloById: function(idpannello) {
            if(idpannello in this.panels) {
                return this.panels[idpannello];
            } else {
                console.log("ERROR getPannelloById '"+idpannello+"'");
            }
        },

        createPannello: function(idpannello) {
            // init a new Panel and set DEFAULT values
            this.panels[idpannello] = new Pannello(idpannello);
            //console.log("create pannello: " + idpannello);

            // Only for individual calls, will be used later.
            //this.panels[idpannello].setIntervalCall();
        },

        initPannelloByArray: function(data) {
            // check if the key idpannello exists
            if("idpannello" in data) {
                var idpannello = data.idpannello;
                // check if we have initialized this panel
                if(idpannello in this.panels) {
                    // set fields values
                    for(var akey in data) {
                        this.panels[idpannello].setValue(akey, data[akey]);
                    }
                    // refresh PANEL
                    this.panels[idpannello].refresh();
                } else {
                    // console.log("ERROR! Panel ID '"+idpannello+"' NOT exists!");
                }
            } else {
                console.log("ERROR! Key 'idpannello' NOT exists!");
            }
        },

        satActiveIdpannelloLive: function(idpannello) {
            // stop the active pannello live if there is one
            if(this.idpannello_live !== null) {
                var pannello = this.getPannelloById(this.idpannello_live);
                pannello.stopLive();
            }
            // set the new one active
            this.idpannello_live = idpannello;
        }

    };

/*
 *  ONE SINGLE Panel class
 */
    function Pannello (idpannello) {
        var self = this;
        self.idpannello = idpannello;
        self.startLive_Interval = false;

        self.fields = {
                idanimal            :0,
                pedometro           :"",
                status              :"",
                number_animal       :0,
                reprod_status       :"",
                lact                :"",
                dim                 :"",
                pannello_status     :PANEL_STATUS_STANDBY,
                produzione          :"",
                produzione_grammi   :0,
                milking_time        :0,
                alarms              :"00",
                ts                  :0,
                conductivity_value  :0,
                temperature_value   :0,
                attenzioni          :"00000000",
                conductivity_exp    :0,
                temperature_exp     :0,
                produzione_exp      :0,
                milking_time_exp    :0,
                alarms_washing      :"00",
                blocco_mungitura    :"00"
            };

        // set fields values
        self.setValue = function(field, value) {
            if(field in self.fields) {
                self.fields[field] = value;
//                console.log(self.idpannello + " --> "+  field + " : " + value);
            }
        };

        // get Pedometro
        self.getPedometro = function () {
            if(self.fields.pedometro !== null && typeof(self.fields.pedometro) === "string") {
                if(self.fields.pedometro !== "0" && self.fields.pedometro.length > 1) {
                    return self.fields.pedometro;
                }
            }
            return null;
        }

        self.isInMilking = function () {
            var milking_in_progress_status = [PANEL_STATUS_TAKEOFF, PANEL_STATUS_MILKING_AUTO, PANEL_STATUS_MILKING_MANUAL, PANEL_STATUS_NON_ATTIVO];
            if( milking_in_progress_status.indexOf(self.fields.pannello_status) != -1 ) {
                return true;
            } else {
                return false;
            }
        };

        self.isInNonAttivo = function () {
            return (self.fields.pannello_status == PANEL_STATUS_NON_ATTIVO);
        };

        self.isInNonAttivoStandby = function () {
            return (self.fields.pannello_status == PANEL_STATUS_NON_ATTIVO_STANDBY);
        };

        self.isInLavaggio = function () {
            return (self.fields.pannello_status == PANEL_STATUS_WASHING);
        };

        self.isInStandby = function () {
            return (self.fields.pannello_status == PANEL_STATUS_STANDBY || self.fields.pannello_status == PANEL_STATUS_NON_ATTIVO_STANDBY);
        };

        self.isInTakeoff = function () {
            return (self.fields.pannello_status == PANEL_STATUS_TAKEOFF);
        };

        // This will be useful for individual panel update with data from iMilk-Net.
        // This will be used later.
        self.update = function() {

            getData = {
                    is_ajax: 1,
                    idpannello: self.idpannello,
                };

            jQuery.ajax({
                type: "GET",
                url: "<?php echo base_url().getClass().'/monitorajax'; ?>",
                dataType: 'json',
                data: getData,
                success: function (response) {
                    //console.log(response);
                    if (response) {

                        if (response.data) {
                            // set fields values
                            for(akey in response.data) {
                                self.setValue(akey, response.data[akey]);
                            }
                            // refresh PANEL
                            self.refresh();
                        }
                    }
                },
                error: function () {
                    console.log('error');
                }
            });

        };

        // refresh the HTML
        self.refresh = function() {
            var pannello_status = self.fields.pannello_status;
            var number_animal = self.fields.number_animal;
            var pannello_html = jQuery("#pannello_" + self.idpannello);

            // INIT HEX VALUES
            var alarms = new HexToBinClass(self.fields.alarms);
            var blocchi_mungitura = new HexToBinClass(self.fields.blocco_mungitura);
            var alarms_washing = new HexToBinClass(self.fields.alarms_washing);
            var attenzioni = new HexToBinClass(self.fields.attenzioni);

            // RESET ALL HTML and CSS VALUES
            pannello_html.find('.graph_image').children('img').hide();
            pannello_html.find(".number_animal").html('');
            pannello_html.find(".prod_reale").html('');
            pannello_html.find(".prod_attesa").html('');
            pannello_html.find(".tempo_prod").html('');
            pannello_html.find(".tempo_prod_exp").html('');
            pannello_html.find(".velos_rep_status").html('');
            pannello_html.find(".lactation").html('');
            pannello_html.find(".lactation_days").html('');
            pannello_html.find(".icon_1").html('');
            pannello_html.find(".icon_2").html('');
            pannello_html.find(".icon_3").html('');
            pannello_html.find(".icon_4").html('');
            pannello_html.find(".conductivity").html('');
            pannello_html.find(".temperatura").html('');
            pannello_html.find(".conductivity_exp").html('');
            pannello_html.find(".temperatura_exp").html('');
            // RESET ALL ALARMS icons
            for (var ak in alarms_status_fields) {
                pannello_html.find("."+alarms_status_fields[ak]).css('background-color','transparent');
            }

            // COLOR pannello_status BACKGROUND
            pannello_html.find(".id_pannello").css('background-color', panelColors[pannello_status]);

            // ICON FOR DIFFERENT STATUS
            if (panelIcons[pannello_status]) {
                pannello_html.find(".icon_milking_mode").html('<img class="icon icon_status_'+pannello_status+'" src="<?php echo $icon_path; ?>' + panelIcons[pannello_status] + '">');
            } else {
                pannello_html.find(".icon_milking_mode").html('');
            }

            // NON ATTIVO
            if(self.isInNonAttivo() || self.isInNonAttivoStandby()) {
                pannello_html.find(".pannello-desc").addClass('pannello-non-attivo');
                // Stop LIVE (show it only in Milking status)
                self.stopLive();
            } else {
                pannello_html.find(".pannello-desc").removeClass('pannello-non-attivo');
            }

            // LAVAGGIO
            if (self.isInLavaggio()) {
                // done nothing, the washing icons will be shown in StandBy status
            }

            // STANDBY
            var status_lavaggio_html = pannello_html.find('.status_lavaggio');
            if(self.isInStandby()) {
                status_lavaggio_html.show();
                if (alarms_washing.isSomeBitEnabled()) {
                    pannello_html.find('.status_lavaggio > td').html('<img src="<?php echo $icon_path; ?>Lavaggio_ERROR.png" class="icon icon_lavaggio">');
                } else {
                    pannello_html.find('.status_lavaggio > td').html('<img src="<?php echo $icon_path; ?>Lavaggio_OK.png" class="icon icon_lavaggio">');
                }
            } else {
                status_lavaggio_html.hide();
            }

            // TAKE-OFF
            if(self.isInTakeoff()) {
                if(self.fields.produzione_grammi > 0) {
                    if (alarms.isSomeBitEnabled()) {
                        // Change color of pannello number background
                        pannello_html.find(".id_pannello").css('background-color', '#ff5c30');
                    } else {
                        // Change color of pannello number background
                        pannello_html.find(".id_pannello").css('background-color', '#00d700');
                    }
                }
            }

            // IN MILKING status: Milking manual and Milking auto
//          console.log(self.fields);
            if(self.isInMilking()) {

                // CHECK IF ANIMAL is in position
                //console.log(self.idpannello + " : " + self.fields.pedometro.length);
                if(self.fields.number_animal > 0 || self.getPedometro() !== null) {

                    // some difference for stato NON ATTIVO
                    if(!self.isInNonAttivo()) {
                        // SHOW button to open Milking graph
                        pannello_html.find('.graph_image').children('img').show();
                    }

                    if(self.fields.number_animal > 0) {
                        pannello_html.find(".number_animal").html(self.fields.number_animal);
                    } else {
                        pannello_html.find(".number_animal").html("<small class='small_pedometro'>" + self.fields.pedometro + "</small>");
                    }
                    //console.log(self.idpannello + " - Pedometro: '" + self.getPedometro() + "'")

                    pannello_html.find(".prod_reale").html(self.fields.produzione);
                    pannello_html.find(".prod_attesa").html(self.fields.produzione_exp);
                    pannello_html.find(".tempo_prod").html(self.fields.milking_time);
                    pannello_html.find(".tempo_prod_exp").html(self.fields.milking_time_exp);
                    pannello_html.find(".velos_rep_status").html(self.fields.reprod_status);
                    pannello_html.find(".lactation").html("Lact: " + self.fields.lact);
                    pannello_html.find(".lactation_days").html("DIM: " + self.fields.dim);

                    // BLOCCHI MUNGITURA
                    var blocco_icon = '';
                    if(blocchi_mungitura.isBitEnabled(BLOCCO_MUNGITURA_MASTITE)) {
                        blocco_icon = "<?php echo $icon_path; ?>Blocco_mastite-ecobuket.png";
                    }
                    if(blocchi_mungitura.isBitEnabled(BLOCCO_MUNGITURA_COLOSTRO)) {
                        blocco_icon = "<?php echo $icon_path; ?>Blocco_mungitura_colostro-ecobuket.png";
                    }
                    if(blocchi_mungitura.isBitEnabled(BLOCCO_MUNGITURA_ANTIBIOTICO)) {
                        blocco_icon = "<?php echo $icon_path; ?>Blocco_mungitura_antibiotico-ecobuket.png";
                    }
                    if(blocchi_mungitura.isBitEnabled(BLOCCO_MUNGITURA_ASCIUTTA)) {
                        blocco_icon = "<?php echo $icon_path; ?>Blocco_mungitura_generico-ecobuket.png";
                    }
                    if(blocco_icon != '') {
                        pannello_html.find(".icon_1").html('<img class="icon" src="'+blocco_icon+'">');
                    }

                    // ATTENZIONI icon display
                    if(attenzioni.isBitEnabled(ATTENZIONI_CALORE)) {
                        pannello_html.find(".icon_2").html('<img class="icon" src="<?php echo $icon_path; ?>Attenzione_calore.png">');
                    }
                    if(attenzioni.isBitEnabled(ATTENZIONI_CALENDARIO)) {
                        pannello_html.find(".icon_3").html('<img class="icon" src="<?php echo $icon_path; ?>Attenzione_calendario.png">');
                    }

                    // ALARMS icon display
                    var alarms_html = '';
                    if( self.isInMilking()) {
                        if (alarms.isSomeBitEnabled()) {
                            alarms_html = '<img class="icon" src="<?php echo $icon_path; ?>Allarme_fine_mungitura.png">';
                        }
                    }
                    pannello_html.find(".icon_4").html(alarms_html);


                    // SET COLOR of ALARMS in particular value
                    for ( var ak in alarms_status_fields ) {
                        if (alarms.isBitEnabled(eval(ak))) {
                            var pannello_alarms = pannello_html.find("." + alarms_status_fields[ak]);
                            if (pannello_alarms.html() != '&nbsp;') {
                                pannello_alarms.css('background-color', '#ff5c30');
                            }
                        }
                    }

                    // OPEN GRAFICO
                    if(self.startLive_Interval) {
                        pannello_html.find(".conductivity").html('Cond&nbsp;<small>(mS)</small>&nbsp;' + self.fields.conductivity_value/10);
                        pannello_html.find(".temperatura").html('Temp&nbsp;<small>(&deg;C)</small>&nbsp;' + self.fields.temperature_value/10);
                        pannello_html.find(".conductivity_exp").html(Math.round(self.fields.conductivity_exp)/10);
                        pannello_html.find(".temperatura_exp").html(Math.round(self.fields.temperature_exp)/10);
                    }

                }


            } else {
                // Stop LIVE (show it only in Milking status)
                self.stopLive();
            }

        };

        // To check status after every call
        self.setIntervalCall = function() {
            window.setInterval(self.update, 3000);
        };

        /********************************************
         * COMPLETE DATA METHODS
         ********************************************/
        // This will be useful for getting live updates of panel with data from iMilk-Net.
        self.live = function() {

            var getData = {
                    is_ajax: 1,
                    idpannello: self.idpannello,
                };

            jQuery.ajax({
                type: "GET",
                url: "<?php echo base_url().getClass().'/liveajax'; ?>",
                dataType: 'json',
                data: getData,
                success: function (response) {
                    //console.log(response);
                    var pos_data = response.data.graph_data;
                    div_name = '#graph_table'+self.idpannello;

                    if (response) {

                        if (response.data) {
                            // set fields values
                            for(akey in response.data) {
                                self.setValue(akey, response.data[akey]);
                            }
                            // refresh PANEL
                            self.refresh();
                            // send a new request to update panel complete data
                            self.updatePanelWithComplete();
                        }
                    draw_histogram(div_name, pos_data);
                    }
                },
                error: function () {
                    console.log('error');
                }
            });

        };

        self.updatePanelWithComplete = function() {

            var postData = {
                    idpannello: self.idpannello,
                };

            jQuery.ajax({
                type: "POST",
                url: "<?php echo base_url().'api/imilk/milking/updatePanelWithComplete'; ?>",
                dataType: 'json',
                data: postData,
                success: function (response) {
                    //console.log(response);
                    if (response) {
                        // success
                    }
                },
                error: function () {
                    console.log('error');
                }
            });

        };

        // START live data
        self.startLive = function() {
            if(self.startLive_Interval === false) {
                // open graph panel
                $('#graph_table'+idpannello).show();
                // start interval
                Panels.satActiveIdpannelloLive(self.idpannello);
                self.startLive_Interval = window.setInterval(self.live, 3000);
            }
        };

        // STOP live data
        self.stopLive = function() {
            if(self.startLive_Interval !== false) {
                // close graph panel
                $('#graph_table' + idpannello).hide();
                // stop interval
                clearInterval(self.startLive_Interval);
                self.startLive_Interval = false;
                self.refresh();
            }
        };

    }

//The drawing of the histogram has been broken out from the data retrial 
    // or computation. (In this case the 'Irwin-Hall distribution' computation above)

    function draw_histogram(reference, pos_data){

        $(reference).empty()
        var chart = c3.generate({
            data: {
                json: pos_data,
                keys: {
                    x: 'x',
                    value: ['y']
                },
                types: {
                    x: 'area',
                    y: 'area'
                }
            },
            axis: {
                x: {
                    show: false
                },
                y: {
                    show: false
                }
            },
            tooltip: {
                show: false,
            },
            legend: {
                show: false
            },
            bindto: reference
        });
    };

    
    
</script>
<style>
    @media only screen and (min-width: 1328px) and (max-width: 1928px) {
        .pannello {
            padding: 5px !important;
        }
    }
</style>
<div id="page-wrapper">

    <div class="row">
        <div class="col-lg-12">
            <div class="page-header">
                <h1><?php echo _('Milking Monitor'); ?></h1>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    <?php echo _('All'); ?> <?php echo _('Milking Monitor'); ?>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div id="milking-container">
                    <?php
                    if ((isset($devicesWithRooms)) && $devicesWithRooms) {
                        foreach ($devicesWithRooms as $iddevice => $rooms) {
                            foreach ($rooms as $idroom => $room) {
                                include 'monitor/'.$room['tipo_sala'].'.php';
                            }
                        }
                    }
                    ?>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /#page-wrapper -->

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="page-header">
                <h1><?php echo _('Utilities - Debug iMilkNET'); ?></h1>
            </div>
        </div>
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-danger">
                <div class="panel-heading">Invia comando</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-7 col-md-offset-1">
                            <form action="utilities/sendcmd" class="form-horizontal" id="sendcmd_form" method="post" accept-charset="utf-8">
                                <div class="form-group">
                                    CMD: <input type="text" class="form-control" id="CMD" name="CMD" value=""><br />
                                    STR: <input type="text" class="form-control" id="STR" name="STR" value="">
                                </div>
                                <button type="submit" class="btn btn-default">INVIA</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

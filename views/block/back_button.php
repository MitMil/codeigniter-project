<?php
if (back_url()) { ?>
    <div class="back_btn_block">
        <span class="back_btn">
            <a href="<?php echo back_url(); ?>" class="btn btn-primary" >
                <i class="fa fa-caret-left"></i>
                <?php echo _('Back'); ?>
            </a>
        </span>
    </div>
    <!--<style type="text/css">
        @media (min-width: 768px) {
            .page-header h1 {
                margin-left: 75px;
            }
            .page-header .back_btn {
                display: block;
                margin-top: -37px;
            }
        }
    </style>-->
<?php
}

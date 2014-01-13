<?php // BEGIN: admin error page ?>
<?php if ($this->layout == 'admin'){ ?>
<?php echo $this->element("admin_navbar_top"); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span2">
            <?php // BEGIN: Navigation bar ?>
            <?php echo GlobalVar::get_html_sidebar_nav(); ?>
            <?php // END: Navigation bar ?>
        </div>

        <div id="content-inner" class="span10">
            <div class="alert alert-error">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <h4>Có lỗi!</h4>
                <?php echo isset($this->error_message)?$this->error_message:''; ?>
            </div>
            <div class="well">
                <?php echo isset($this->error_detail)?$this->error_detail:''; ?>
            </div>
        </div>
        <hr>
    </div>
</div>
<?php // END: admin error page ?>

<?php // BEGIN: app error page ?>
<?php } else { ?>
<?php echo $this->element("app_navbar_top"); ?>
<section id="content-inner">
    <div class="alert alert-error">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <h4>Có lỗi!</h4>
        <?php echo isset($this->error_message)?$this->error_message:''; ?>
    </div>
    <div class="well">
        <?php echo isset($this->error_detail)?$this->error_detail:''; ?>
    </div>
</section>
<?php }?>
<?php // END: app error page ?>
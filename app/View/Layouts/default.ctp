<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php echo $this->Html->charset(); ?>
        <title>
            <?php echo $title_for_layout; ?>
        </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta property="og:title" content="<?php echo $title_for_layout; ?>"/>
       

        <?php
        if (isset($og_img)) {
            echo "<meta property=\"og:type\" content=\"activity\"/>\n";
            echo "<meta property=\"og:url\" content=\"" . $og_quiz_url . "\"/>\n";
            echo "<meta property=\"og:image\" content=\"" . $og_img . "\"/>\n";
            echo "<meta property=\"og:site_name\" content=\"\"/>\n";
            echo "<meta property=\"og:description\" content=\"" . $og_description . "\"/>\n";
            echo "<meta property=\"fb:app_id\" content=\"" . FB_APP_ID . "\"/>\n";
        }
        echo $this->Html->meta('icon');
        echo $this->Html->css(ROOT_URL . 'jquery/bootstrap/css/bootstrap.css');
        echo $this->Html->css(ROOT_URL . 'jquery/bootstrap/css/bootstrap-responsive.min.css');
        
        
        $user_agent = new UserAgentInfo();
        if ($user_agent->isPC()) {
            echo $this->Html->css(ROOT_URL . 'css/layouts.css');
        } else {
            echo $this->Html->css(ROOT_URL . 'css/layouts_sp.css');
        }
        ?>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
        <base href="<?php echo ROOT_URL; ?>">
            <?php  echo GA_CODE; ?>   
            <!-- dfp start -->
            <?php 
                echo $this->Html->script(ROOT_URL . 'js/common.js');
                echo $this->Html->script(ROOT_URL . 'jquery/bootstrap/js/bootstrap.js');
            ?>
           


            <!-- dfp end -->
    </head>
    <body>
        <div id="fb-root"></div>
        <?php // -- BEGIN: Page content ?>
        <div id="container" class="container" >

            <?php echo $this->Session->flash(); ?>

            <?php echo $this->fetch('content'); ?>
        </div>
        <?php // -- END: Page content  ?>


    </body>
</html>

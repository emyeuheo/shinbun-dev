<?php echo $this->element("app_navbar_top"); ?>
<section id="content-inner">
    <div class="container-fluid">        
    </div>
    
    <div class="container-fluid">
        <ul class="breadcrumb">
            <li class="active"><i class="icon-gift"></i>&nbsp;No registration any more, just sign-in!</li>
        </ul>
        
        <div class="row">
          <div class="span4"><div id="facebook_login"><img src="img/facebook-login.png" alt="Facebook"></div></div>
          <div class="span4"><div id="twitter_login"><img src="img/twitter-login.png" alt="Twitter"></div></div>
        </div>
        
    </div>
</section>
<script>
$(document).ready(function(){
    $('#facebook_login').click(function(){
        openLoginDialog('aaaaa');
    });
});
</script>
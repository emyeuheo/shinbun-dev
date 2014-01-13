<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container-fluid">
      <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <a class="brand" href="#">WTAアプリ </a>
      <div class="nav-collapse collapse">
        <p class="navbar-text pull-right">ログインしていません</p>
        <ul class="nav">
          <li class="active"><a href="<?php echo ADMIN_HOME_URL; ?>">ホーム</a></li>
        </ul>
      </div><!--/.nav-collapse -->
    </div>
  </div>
</div>
<div class="container">
    <div class="well" style="margin-left: auto; margin-right: auto ; width: 220px;">
        <form id="login_form" method="post" action="admin/login">
            <legend><i class="icon-user"></i>&nbsp;ログイン</legend>
            <?php echo $this->Session->flash(); ?>
            <div class="controls">
                <label class="control-label" for="username">ユーザ名</label>
                <input type="text" id="username" name="username"/><br/>
                <label class="control-label" for="password">パスワード</label>
                <input type="password" id="password" name="password"/><br/>
            </div>
            <input type="submit" id="login" class ="btn btn-primary btn-large" name="login" value="ログイン"/>
            <input type="hidden" id="returnurl" name="returnurl" value="<?php echo isset($returnurl) ? $returnurl : ''; ?>"/>
        </form>
    </div>
</div>
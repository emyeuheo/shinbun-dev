<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container-fluid">
      <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <a class="brand" href="<?php echo ADMIN_HOME_URL; ?>">WTAアプリ</a>
      <div class="nav-collapse collapse">
        <p class="navbar-text pull-right">
          	ログイン <a href="<?php echo ADMIN_HOME_URL; ?>" class="navbar-link"><?php echo $this->Session->read(SESSION_USER) ?></a>&nbsp;|&nbsp;<a class="btn btn-mini" href="<?php echo ADMIN_HOME_URL.'logout'; ?>">ログアウト</a>
        </p>
        <ul class="nav">
          <li class="active"><a href="<?php echo ADMIN_HOME_URL; ?>">ホーム</a></li>
          
        </ul>
      </div><!--/.nav-collapse -->
    </div>
  </div>
</div>
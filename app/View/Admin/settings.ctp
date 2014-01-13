<?php echo $this->element("admin_navbar_top");
?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span2">
            <?php // BEGIN: Navigation bar ?>
            <?php echo GlobalVar::get_html_sidebar_nav(); ?>
            <?php // END: Navigation bar ?>
        </div>

        <div id="content-inner" class="span10">
            <form  class="form-horizontal">
                <ul class="breadcrumb" style="margin-bottom: 5px;">
                    <li class="active">環境情報情報</li>
                </ul>
                <p>■　環境:　<span class="label label-important"><?php echo SYSTEM_EVIRONMENT; ?></span></p>
                <p>■　サーバー名:　<?php echo SERVER_NAME; ?></p>
                <p>■　バージョン:　<?php echo VERSION; ?></p>
                <ul class="breadcrumb" style="margin-bottom: 5px;">
                    <li class="active">データベース設定</li>
                </ul>
                <div class="control-group" >
                    <label class="control-label" for="db_host">ホスト名</label>
                    <div class="controls" >
                        <input type="text" name="db_host" id="db_host" value="<?php echo isset($setting['db_host']) ? $setting['db_host'] : "" ?>" class="span4 edit-item">
                    </div>
                </div>
                <div class="control-group" >
                    <label class="control-label" for="db_name">データベース名</label>
                    <div class="controls" >
                        <input type="text" name="db_name" id="db_name" value="<?php echo isset($setting['db_name']) ? $setting['db_name'] : "" ?>" class="span4 edit-item">
                    </div>

                </div>
                <div class="control-group" >
                    <label class="control-label" for="db_port">ポート</label>
                    <div class="controls" >
                        <input type="text" name="db_port" id="db_port" value="<?php echo isset($setting['db_port']) ? $setting['db_port'] : "" ?>" class="span1 edit-item">
                    </div>
                </div>
                <div class="control-group" >
                    <label class="control-label" for="db_user">ユーザ名</label>
                    <div class="controls" >
                        <input type="text" name="db_user" id="db_user" value="<?php echo isset($setting['db_user']) ? $setting['db_user'] : "" ?>" class="span4 edit-item">
                    </div>
                </div>

                <div class="control-group" >
                    <label class="control-label" for="db_password">パスワード</label>
                    <div class="controls" >
                        <input type="text" name="db_password" id="db_password" value="<?php echo isset($setting['db_password']) ? $setting['db_password'] : "" ?>" class="span4 edit-item">
                    </div>
                </div>


                <ul class="breadcrumb" style="margin-bottom: 5px;">
                    <li class="active">Facebookアプリ設定</li>
                </ul>
                <div class="control-group" >
                    <label class="control-label" for="fb_app_id">アプリID</label>
                    <div class="controls" >
                        <input type="text" name="fb_app_id" id="fb_app_id" value="<?php echo isset($setting['fb_app_id']) ? $setting['fb_app_id'] : "" ?>" class="span4 edit-item">
                    </div>
                </div>
                <div class="control-group" >
                    <label class="control-label" for="fb_app_name">アプリ名</label>
                    <div class="controls" >
                        <input type="text" name="fb_app_name" id="fb_app_name" value="<?php echo isset($setting['fb_app_name']) ? $setting['fb_app_name'] : "" ?>" class="span4 edit-item">
                    </div>
                </div>
                <div class="control-group" >
                    <label class="control-label" for="fb_app_secret">アプリシークレット</label>
                    <div class="controls" >
                        <input type="text" name="fb_app_secket" id="fb_app_secret" value="<?php echo isset($setting['fb_app_secret']) ? $setting['fb_app_secret'] : "" ?>" class="span4 edit-item">
                    </div>
                </div>
                <div class="control-group" >
                    <label class="control-label" for="fb_info_fields">Facebookから取得項目</label>
                    <div class="controls" >
                        <input type="text" name="fb_info_fields" id="fb_info_fields" value="<?php echo isset($setting['fb_info_fields']) ? $setting['fb_info_fields'] : "" ?>" class="span10 edit-item">
                    </div>
                </div>
                <div class="control-group" >
                    <label class="control-label" for="fb_permission">要求権限</label>
                    <div class="controls" >
                        <input type="text" name="fb_permission" id="fb_permission" value="<?php echo isset($setting['fb_permission']) ? $setting['fb_permission'] : "" ?>" class="span10 edit-item">
                    </div>
                </div>
                <div class="control-group" >
                    <label class="control-label" for="fb_page_username">ページユーザ名</label>
                    <div class="controls" >
                        <input type="text" name="fb_page_username" id="fb_page_username" value="<?php echo isset($setting['fb_page_username']) ? $setting['fb_page_username'] : "" ?>" class="span4 edit-item">
                    </div>
                </div>
                <div class="control-group" >
                    <label class="control-label" for="fb_page_id">ページID</label>
                    <div class="controls" >
                        <input type="text" name="fb_page_id" id="fb_page_id" value="<?php echo isset($setting['fb_page_id']) ? $setting['fb_page_id'] : "" ?>" class="span4 edit-item">
                    </div>
                </div>
                <div class="control-group" >
                    <label class="control-label" for="fb_page_name">ページ名</label>
                    <div class="controls" >
                        <input type="text" name="fb_page_name" id="fb_page_name" value="<?php echo isset($setting['fb_page_name']) ? $setting['fb_page_name'] : "" ?>" class="span4 edit-item">
                    </div>
                </div>
                <div class="control-group" >
                    <label class="control-label" for="page_name_html">ページ名HTML</label>
                    <div class="controls" >
                        <textarea class="span10 edit-item" id="page_name_html" name="page_name_html" rows="5"><?php echo isset($setting['page_name_html']) ? $setting['page_name_html'] : "" ?></textarea>  
                    </div>
                </div>

                <ul class="breadcrumb" style="margin-bottom: 5px;">
                    <li class="active">スパイラルDB設定</li>
                </ul>

                <div class="control-group" >
                    <label class="control-label" for="spiral_api_url">API URL</label>
                    <div class="controls" >
                        <input type="text" name="spiral_api_url" id="spiral_api_url" value="<?php echo isset($setting['spiral_api_url']) ? $setting['spiral_api_url'] : "" ?>" class="span4 edit-item">

                    </div>
                </div>
                <div class="control-group" >
                    <label class="control-label" for="spiral_transaction">トランザクションDB名</label>
                    <div class="controls" >
                        <input type="text" name="spiral_transaction" id="spiral_transaction" value="<?php echo isset($setting['spiral_transaction']) ? $setting['spiral_transaction'] : "" ?>" class="span4 edit-item">
                    </div>
                </div>


                <div class="control-group" >
                    <label class="control-label" for="spiral_token">APIトークン</label>
                    <div class="controls" >
                        <input type="text" name="spiral_token" id="spiral_token" value="<?php echo isset($setting['spiral_token']) ? $setting['spiral_token'] : "" ?>" class="span10 edit-item">
                    </div>
                </div>

                <div class="control-group" >
                    <label class="control-label" for="spiral_token_secret">APIトークンシークレット</label>
                    <div class="controls" >
                        <input type="text" name="spiral_token_secret" id="spiral_token_secret" value="<?php echo isset($setting['spiral_token_secret']) ? $setting['spiral_token_secret'] : "" ?>" class="span10 edit-item">
                    </div>
                </div>

                <div class="control-group" >
                    <label class="control-label" for="spiral_thanks_id">配信ルールID</label>
                    <div class="controls" >
                        <input type="text" name="spiral_thanks_id" id="spiral_thanks_id" value="<?php echo isset($setting['spiral_thanks_id']) ? $setting['spiral_thanks_id'] : "" ?>" class="span4 edit-item">
                    </div>
                </div>
                <div class="control-group" >
                    <label class="control-label" for="spiral_thanks_send">配信する？</label>
                    <div class="controls" >
                        <select name="spiral_thanks_send" id="spiral_thanks_send"class="edit-item span1">
                            <option value="TRUE" <?php echo (isset($setting['spiral_thanks_send']) && $setting['spiral_thanks_send']) ? "selected" : "" ?>>する</option>
                            <option value="FALSE" <?php echo (isset($setting['spiral_thanks_send']) && !$setting['spiral_thanks_send']) ? "selected" : "" ?>>しない</option>
                        </select>
                    </div>
                </div>
                <ul class="breadcrumb" style="margin-bottom: 5px;">
                    <li class="active">広告設定</li>
                </ul>

                
                <div class="control-group" >
                    <label class="control-label" for="dfp_pc_index">DFP PC INDEX (728x90)</label>
                    <div class="controls" >
                        <textarea class="span10 edit-item" id="dfp_pc_index" name="dfp_pc_index" rows="5"><?php echo isset($setting['dfp_pc_index']) ? $setting['dfp_pc_index'] : "" ?></textarea>  
                    </div>
                </div>
                 <div class="control-group" >
                    <label class="control-label" for="dfp_pc_quiz_left">DFP PC QUIZ LEFT (300x250)</label>
                    <div class="controls" >
                        <textarea class="span10 edit-item" id="dfp_pc_quiz_left" name="dfp_pc_quiz_left" rows="5"><?php echo isset($setting['dfp_pc_quiz_left']) ? $setting['dfp_pc_quiz_left'] : "" ?></textarea>  
                    </div>
                </div>
                 <div class="control-group" >
                    <label class="control-label" for="dfp_pc_quiz_right">DFP PC QUIZ RIGHT (300x250)</label>
                    <div class="controls" >
                        <textarea class="span10 edit-item" id="dfp_pc_quiz_right" name="dfp_pc_quiz_right" rows="5"><?php echo isset($setting['dfp_pc_quiz_right']) ? $setting['dfp_pc_quiz_right'] : "" ?></textarea>  
                    </div>
                </div>
                <div class="control-group" >
                    <label class="control-label" for="dfp_pc_result">DFP PC RESULT</label>
                    <div class="controls" >
                        <textarea class="span10 edit-item" id="dfp_pc_result" name="dfp_pc_result" rows="5"><?php echo isset($setting['dfp_pc_result']) ? $setting['dfp_pc_result'] : "" ?></textarea>  
                    </div>
                </div>
                <div class="control-group" >
                    <label class="control-label" for="dfp_sp_index">DFP SP INDEX (300x250)</label>
                    <div class="controls" >
                        <textarea class="span10 edit-item" id="dfp_sp_index" name="dfp_sp_index" rows="5"><?php echo isset($setting['dfp_sp_index']) ? $setting['dfp_sp_index'] : "" ?></textarea>  
                    </div>
                </div>
                <div class="control-group" >
                    <label class="control-label" for="dfp_sp_quiz">DFP SP QUIZ</label>
                    <div class="controls" >
                        <textarea class="span10 edit-item" id="dfp_sp_quiz" name="dfp_sp_quiz" rows="5"><?php echo isset($setting['dfp_sp_quiz']) ? $setting['dfp_sp_quiz'] : "" ?></textarea>  
                    </div>
                </div>
                <div class="control-group" >
                    <label class="control-label" for="dfp_sp_result">DFP SP RESULT (300x250)</label>
                    <div class="controls" >
                        <textarea class="span10 edit-item" id="dfp_sp_result" name="dfp_sp_result" rows="5"><?php echo isset($setting['dfp_sp_result']) ? $setting['dfp_sp_result'] : "" ?></textarea>  
                    </div>
                </div>
               
                <div class="control-group" >
                    <label class="control-label" for="dfp_overlay">DFP OVERLAY</label>
                    <div class="controls" >
                        <textarea class="span10 edit-item" id="dfp_overlay" name="dfp_overlay" rows="5"><?php echo isset($setting['dfp_overlay']) ? $setting['dfp_overlay'] : "" ?></textarea>  
                    </div>
                </div>
                

                <ul class="breadcrumb" style="margin-bottom: 5px;">
                    <li class="active">その他</li>
                </ul>

                <div class="control-group" >
                    <label class="control-label" for="app_name">アプリ名</label>
                    <div class="controls" >
                        <input type="text" name="app_name" id="app_name" value="<?php echo isset($setting['app_name']) ? $setting['app_name'] : "" ?>" class="span4 edit-item">

                    </div>
                </div>
                <div class="control-group" >
                    <label class="control-label" for="row_per_page">ページ毎のロー</label>
                    <div class="controls" >
                        <input type="text" name="row_per_page" id="row_per_page" value="<?php echo isset($setting['row_per_page']) ? $setting['row_per_page'] : "" ?>" class="span1 edit-item">

                    </div>
                </div>
                <div class="control-group" >
                    <label class="control-label" for="google_analytics">グーグルアナリティクス</label>
                    <div class="controls" >
                        <textarea class="span10 edit-item" id="google_analytics" name="google_analytics" rows="5"><?php echo isset($setting['google_analytics']) ? $setting['google_analytics'] : "" ?></textarea>  
                    </div>
                </div>
                <div class="control-group" >
                    <label class="control-label" for="ads_link">広告掲載</label>
                    <div class="controls" >
                        <input type="text" name="ads_link" id="ads_link" value="<?php echo isset($setting['ads_link']) ? $setting['ads_link'] : "" ?>" class="span10 edit-item">

                    </div>
                </div>
                <div class="control-group" >
                    <label class="control-label" for="company_link">会社概要</label>
                    <div class="controls" >
                        <input type="text" name="company_link" id="company_link" value="<?php echo isset($setting['company_link']) ? $setting['company_link'] : "" ?>" class="span10 edit-item">

                    </div>
                </div>
                <div class="control-group" >
                    <label class="control-label" for="terms_link">利用規約</label>
                    <div class="controls" >
                        <input type="text" name="terms_link" id="terms_link" value="<?php echo isset($setting['terms_link']) ? $setting['terms_link'] : "" ?>" class="span10 edit-item">

                    </div>
                </div>
                <div class="control-group" >
                    <label class="control-label" for="privacy_link">プライバシーリンク</label>
                    <div class="controls" >
                        <input type="text" name="privacy_link" id="privacy_link" value="<?php echo isset($setting['privacy_link']) ? $setting['privacy_link'] : "" ?>" class="span10 edit-item">

                    </div>
                </div>
                

                <div class="form-actions">
                    <input id="btn-edit" type="button" class="btn " value="編集">
                    <input id="btn-save" type="button" class="btn btn-info" value="設定">
                </div>
            </form>
        </div>
        <hr>
    </div>
</div>

<div class="modal hide fade modal-confirm">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="center">確認</h3>
    </div>
    <div class="modal-body">
        <p>設定が保存されました</p>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn btn-primary" data-dismiss="modal" aria-hidden="true">OK</a>
    </div>
</div>

<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>確認</h3>
    </div>
    <div class="modal-body">
        <p>変更してよろしいですか</p>
    </div>
    <div class="modal-footer">
        <button type="button"  id="change" class="btn btn-primary" data-dismiss="modal" aria-hidden="true" >変更する</button>                                   
    </div>
</div>
<script type="text/javascript">


    $(function() {
        $('.edit-item').prop('disabled', true);
        $('#btn-save').prop('disabled', true);
        $('#btn-edit').click(function() {
            if ($(this).val() == '編集') {
                $('.edit-item').prop('disabled', false);
                $('#btn-save').prop('disabled', false);
                $(this).val('キャセル');
            } else {
                $('.edit-item').prop('disabled', true);
                $(this).val('編集');
                $('#btn-save').prop('disabled', true);
            }

        });
        $('#change').click(function() {
            $.post('admin/settings', {
                db_host: $('#db_host').val(),
                db_name: $('#db_name').val(),
                db_port: $('#db_port').val(),
                db_user: $('#db_user').val(),
                db_password: $('#db_password').val(),
                fb_app_id: $('#fb_app_id').val(),
                fb_app_name: $('#fb_app_name').val(),
                fb_app_secret: $('#fb_app_secret').val(),
                fb_page_username: $('#fb_page_username').val(),
                fb_page_name: $('#fb_page_name').val(),
                fb_page_id: $('#fb_page_id').val(),
                fb_permission: $('#fb_permission').val(),
                page_name_html: $('#page_name_html').val(),
                fb_info_fields: $('#fb_info_fields').val(),
                spiral_api_url: $('#spiral_api_url').val(),
                spiral_transaction: $('#spiral_transaction').val(),
                spiral_normal: $('#spiral_normal').val(),
                spiral_token: $('#spiral_token').val(),
                spiral_token_secret: $('#spiral_token_secret').val(),
                spiral_thanks_id: $('#spiral_thanks_id').val(),
                spiral_thanks_send: $('#spiral_thanks_send').val(),
                dfp_pc_index: $('#dfp_pc_index').val(),
                dfp_pc_result: $('#dfp_pc_result').val(),
                dfp_sp_index: $('#dfp_sp_index').val(),
                dfp_sp_quiz: $('#dfp_sp_quiz').val(),
                dfp_sp_result: $('#dfp_sp_result').val(),
                dfp_overlay: $('#dfp_overlay').val(),
                dfp_pc_quiz_left: $('#dfp_pc_quiz_left').val(),
                dfp_pc_quiz_right: $('#dfp_pc_quiz_right').val(),
                app_name: $('#app_name').val(),
                row_per_page: $('#row_per_page').val(),
                google_analytics: $('#google_analytics').val(),
                ads_link: $('#ads_link').val(),
                company_link: $('#company_link').val(),
                privacy_link: $('#privacy_link').val(),
                terms_link: $('#terms_link').val(),
            }, function(data) {
                $('.modal-confirm').modal();
                $('.edit-item').prop('disabled', true);
                $('#btn-edit').val('編集');
                $('#btn-save').prop('disabled', true);
            })
        });
        $('#btn-save').click(function() {
            $('#myModal').modal();

        });
    })
</script>
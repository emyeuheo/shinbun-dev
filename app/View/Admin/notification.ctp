<?php echo $this->element("admin_navbar_top"); ?>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js"></script>
<div class="container-fluid">
    <div class="row-fluid">  
        <div class="span2"> 
            <?php // BEGIN: Navigation bar ?>
            <?php echo GlobalVar::get_html_sidebar_nav(); ?>
            <?php // END: Navigation bar ?>
        </div>  
        <div id="content-inner" class="span10">
            <?php // BEGIN: form ?>
            <div class="well">
                <form class="form-horizontal" id="send_notification" >
                    <legend><i class="icon-question-sign"></i>&nbsp;通知</legend>
                    <?php echo $this->Session->flash(); // báo lỗi ?>

                    <div class="control-group">		                	
                        <label class="control-label" for="url" >URL</label>            
                        <div class="controls">
                            <input type="text" class="span10" id="url_input" name="url_input" placeholder="相対なリンクを入力してください" value="<?php echo isset($url_input) ? $url_input : '' ?>" />
                        </div>
                    </div>

                    <div class="control-group">		                	
                        <label class="control-label" for="msg_notify">メッセージ</label>            
                        <div class="controls">
                            <textarea class="span10" id="msg_notify" name="msg_notify" rows="3" placeholder="メッセージを入力してください"></textarea>  

                        </div>
                    </div>
                    <div class="control-group">		                	
                        <label class="control-label" for="gender">性別 </label>            
                        <div class="controls">
                            <?php
                            echo $this->Form->input('gender', array(
                                'options' => array('0' => '全て', '1' => '男性', '2' => '女性'),
                                'id' => 'gender',
                                'class' => 'span2',
                                'label' => false,
                                'div' => false,
                                'required' => false
                            ));
                            ?>

                        </div>
                    </div>
                    <div  class="control-group">
                        <label class="control-label">年齢指定</label>
                        <div  class="controls">
                            <input type="checkbox" name="age_assign"  id="age_assign" />
                            <input class="span1" type="text" id="from_age" name="from_age"  value="<?php echo isset($from_age) ? $from_age : '' ?>" />  から <input class="span1" type="text" id="to_age" name="to_age"  value="<?php echo isset($to_age) ? $to_age : '' ?>" />  まで
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls">
                            <div class="form-actions">
                                <a href="#myModal" id="nextBtn" role="button" class="btn btn-info" data-toggle="modal">確認</a> &nbsp;
                                <button type="button" id="cancel" class="btn" name="cancel">キャンセル</button>
                                <div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        <center><h3 id="myModalLabel">確認</h3></center>
                                    </div>
                                    <div class="modal-body">
                                        <pre id="modal-body-content">
                                          
                                        </pre>
                                        <div id="lbl_loading"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button"  id="post_notify" class="btn btn-info" name="post_notify"> 通知する</button>                                   
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>   
                <hr>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {

        $("#post_notify").click(function() {
            $.ajax({
                type: 'POST',
                url: " admin/notification/",
                data: {
                    href: $('#url_input').val(),
                    msg_notify: $("#msg_notify").val(),
                    gender: $("#gender").val(),
                    age_assign: $('#age_assign').is(':checked') ? 1 : 0,
                    from_age: $("#from_age").val(),
                    to_age: $("#to_age").val()
                },
                async: true,
                beforeSend: function(data) {
                    $("#lbl_loading").html("<center>" + '送信中...' + '<?php echo $this->Html->image('AjaxLoader.gif'); ?>' + "</center>");
                    $('#post_notify').prop('disabled', true);
                },
                success: function(data) {
                    //console.log("response from server: " + data);
                    $("#lbl_loading").html("<center><label>" + "送信が完了しました! " + "</label></center>");
                }
            });


        });
        $("#nextBtn").click(function() {
            $('#post_notify').prop('disabled', false);
            $("#lbl_loading").html('');
            var modal_text = "URL: <a href='" + getURL() + "' target='_blank'>" + getURL() + "</a><br>"
                    + "メッセージ: " + $('#msg_notify').val() + "<br>"
                    + "性別: " + getGender();
            if ($('#age_assign').is(':checked')) {
                modal_text += "<br>年齢: " + $('#from_age').val() + "から" + $('#to_age').val() + "まで";
            }
            $("#modal-body-content").html(modal_text);
        });
        function getGender() {
            if ($('#gender').val() == 0) {
                return "指定なし";
            } else if ($('#gender').val() == 1) {
                return "男性";
            } else {
                return "女性";
            }
        }
        function getURL() {
            return 'https://apps.facebook.com/<?php echo FB_APP_NAME ?>/' + $('#url_input').val();
        }
    })

</script>

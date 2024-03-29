
<div class="container-fluid">
    <div id="content-inner">
        <table style="<?php echo $table_style; ?>" class="zoom zoom-quiz">
            <tr>
                <td >
                    <div class="logo header">
                        <a href="<?php echo ROOT_URL ?>"><img src="<?php echo $header_img ?>"/></a>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="transparent-border">
                        <div>
                            <center> <?php echo $this->Html->image('quiz_pc_100.jpg') ?></center>
                        </div>
                        <br>
                        <div class="clearfix"></div>
                        <div id ="form-framefb"  class="hidefb">
                            <div class="fb-like-frame">
                                <div class="title-border-quiz">まずはいいね！を押してね</div>
                                <div class="inner-div-quiz">
                                    <div  id="like_button_holder">
                                        <div  id="fb-like-box"  class="fb-like" data-send="false" data-layout="button_count" data-width="200" data-href="http://www.facebook.com/<?php echo FB_PAGE_USERNAME ?>" data-show-faces="true">
                                        </div>
                                    </div>
                                    <div class="link-pressed">
                                        <a id='link-pressed' target='_blank' href='http://www.facebook.com/<?php echo FB_PAGE_USERNAME ?>'><?php echo FB_PAGE_NAME ?></a>  
                                    </div>
                                </div>
                            </div>
                            <div id="quiz_answer_btn" class="pagination-centered" >
                                <a id="view_answer_result"   target="_top" class="btn btn-primary size-btn result-text"  href="<?php echo ROOT_URL ?>shinbun/result"> <?php echo '<span class="result-text">⇒ シェア＆新聞を作る </span>'; ?></a>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="transparent-border-dfp">
                    <div class="center">
                        <?php
                        if ($pc == 'pc') {
                            echo'
                        <div class="body_dfp center" id="dfp_pc_quiz_left" > 
                        </div>
                        <div class="body_dfp center" id="dfp_pc_quiz_right" > 
                        </div>';
                        } else {
                            echo '<div class="body_dfp center margin-dfp" id="dfp_sp_quiz"> 
                            </div>';
                        }
                        ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo $this->element('footer', array(), array('cache' => true)); ?>
                </td>
            </tr>
        </table>            
    </div>

</div>

<script type="text/javascript">
    var like_button_clicked = false;
    var authorized = false;
    window.fbAsyncInit = function() {
        FB.init({
            appId: '<?php echo FB_APP_ID; ?>', // App ID
            status: true, // check login status
            cookie: true, // enable cookies to allow the server to access the session
            xfbml: true, // parse XFBML
            //            channelUrl : 'http://be.comnico.jp/fb/channel/ja_JP',
            oauth: true
        });

        FB.Event.subscribe('edge.create', function(response) {
            like_button_clicked = true;
            ga('send', 'social', 'facebook', 'like', response);
        }
        );
        FB.Event.subscribe('edge.remove', function(response) {
            like_button_clicked = false;
            ga('send', 'social', 'facebook', 'unlike', response);
        }
        );
        FB.getLoginStatus(function(response) {
            //The content in form-framefb become visible from here

            $("#form-framefb").removeClass("hidefb");
            if (response.status === 'connected') { // already connected
                authorized = true;
                var user_id = response.authResponse.userID;
                page_id = <?php echo FB_FAN_PAGE_ID; ?>;

                FB.api({
                    method: 'fql.multiquery',
                    queries: {
                        query1: "SELECT sex, birthday_date FROM user WHERE uid =" + user_id,
                        query2: "SELECT uid FROM page_fan WHERE page_id =" + page_id + " and uid=" + user_id
                    }
                },
                function(response) {
<?php if ($pc == 'pc') { ?>
                        insert_ads(response[0].fql_result_set[0], "dfp_pc_quiz_left");
                        insert_ads(response[0].fql_result_set[0], "dfp_pc_quiz_right");
<?php } else { ?>
                        insert_ads(response[0].fql_result_set[0], "quiz-header-dfp");
                        insert_ads(response[0].fql_result_set[0], "dfp_sp_quiz");
<?php } ?>
                    if (response[1].fql_result_set.length == 1) {
                        like_button_clicked = true;
                    }
                    else {
                        like_button_clicked = false;
                    }
                }
                );


            } else if (response.status === 'not_authorized') {
                authorized = false;
<?php if ($pc == 'pc') { ?>
                    insert_ads(null, "dfp_pc_quiz_left");
                    insert_ads(null, "dfp_pc_quiz_right");
<?php } else { ?>
                    insert_ads(null, "quiz-header-dfp");
                    insert_ads(null, "dfp_sp_quiz");
<?php } ?>
                $("#view_answer_result").html('<?php echo '<b id="abc">⇒ アプリインストールする</b>'; ?>');

                $("#view_answer_result").attr('href', "<?php echo CALLBACK_URI . "facebook/" ?>");
            }
            else {//unknown
                //Modified old version
                authorized = false;
<?php if ($pc == 'pc') { ?>
                    insert_ads(null, "dfp_pc_quiz_left");
                    insert_ads(null, "dfp_pc_quiz_right");
<?php } else { ?>
                    insert_ads(null, "quiz-header-dfp");
                    insert_ads(null, "dfp_sp_quiz");
<?php } ?>
                $("#view_answer_result").html('<?php echo '<span  style="font-size:18px;font-weight:bold">アプリインストール</span>'; ?>');
                $('#view_answer_result').attr('href', "https://www.facebook.com/dialog/oauth?client_id=<?php echo FB_APP_ID ?>&redirect_uri=<?php echo CALLBACK_URI . "shinbun/" ?>&response_type=token&grant_type=client_credentials&scope=<?php echo FB_PERMISSIONS ?>");
            }
        });
    };

    function insert_ads(response, id) {
        var s = document.getElementById(id);
        var define_slot;
        if (id == 'quiz-header-dfp') {//quiz-header-dfp
            define_slot = "<?php echo DFP_OVERLAY; ?>";
        }
        else if (id == 'dfp_pc_quiz_left') {
            define_slot = "<?php echo DFP_PC_QUIZ_LEFT; ?>";
        }
        else if (id == 'dfp_pc_quiz_right') {
            define_slot = "<?php echo DFP_PC_QUIZ_RIGHT; ?>";
        }
        else if (id == 'dfp_sp_quiz') {
            define_slot = "<?php echo DFP_SP_QUIZ; ?>";
        }
        var slots = define_slot.split(',');
        var ads_id = slots[slots.length - 1];
        ads_id = ads_id.replace(/'/g, '');
        ads_id = $.trim(ads_id);
        if (s) {
            if (response) {
                var dob = new Date(response.birthday_date);
                var today = new Date();
                var age = Math.floor((today - dob) / (365.25 * 24 * 60 * 60 * 1000));
                s.id = ads_id;
                googletag.cmd.push(function() {
                    if (id == 'quiz-header-dfp') {//quiz-header-dfp
                        googletag.defineSlot(<?php echo DFP_OVERLAY; ?>)
                                .addService(googletag.pubads())
                                //.setTargeting("age", '"' + age + '"')
                                .setTargeting("gender", response.sex);

                    }
                    else if (id == 'dfp_pc_quiz_left') {
                        googletag.defineSlot(<?php echo DFP_PC_QUIZ_LEFT; ?>)
                                .addService(googletag.pubads())
                                // .setTargeting("age", '"' + age + '"')
                                .setTargeting("gender", response.sex);

                    }
                    else if (id == 'dfp_pc_quiz_right') {
                        googletag.defineSlot(<?php echo DFP_PC_QUIZ_RIGHT; ?>)
                                .addService(googletag.pubads())
                                //.setTargeting("age", '"' + age + '"')
                                .setTargeting("gender", response.sex);

                    }
                    else if (id == 'dfp_sp_quiz') {
                        googletag.defineSlot(<?php echo DFP_SP_QUIZ; ?>)
                                .addService(googletag.pubads())
                                // .setTargeting("age", '"' + age + '"')
                                .setTargeting("gender", response.sex);

                    }
                    googletag.pubads().enableSingleRequest();
                    googletag.enableServices();
                    googletag.display(ads_id);
                });
            }
            else {
                s.id = ads_id;
                googletag.cmd.push(function() {
                    if (id == 'quiz-header-dfp') {//quiz-header-dfp
                        googletag.defineSlot(<?php echo DFP_OVERLAY; ?>)
                                .addService(googletag.pubads());
                    }
                    else if (id == 'dfp_pc_quiz_left') {
                        googletag.defineSlot(<?php echo DFP_PC_QUIZ_LEFT; ?>)
                                .addService(googletag.pubads());
                    }
                    else if (id == 'dfp_pc_quiz_right') {
                        googletag.defineSlot(<?php echo DFP_PC_QUIZ_RIGHT; ?>)
                                .addService(googletag.pubads());
                    }
                    else if (id == 'dfp_sp_quiz') {
                        googletag.defineSlot(<?php echo DFP_SP_QUIZ; ?>)
                                .addService(googletag.pubads());
                    }
                    googletag.pubads().enableSingleRequest();
                    googletag.enableServices();
                    googletag.display(ads_id);
                });
            }
        }
    }

    (function(d, debug) {
        var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
        if (d.getElementById(id)) {
            return;
        }
        js = d.createElement('script');
        js.id = id;
        js.async = true;
        js.src = "//connect.facebook.net/ja_JP/all" + (debug ? "/debug" : "") + ".js";
        ref.parentNode.insertBefore(js, ref);
    }(document, /*debug*/false));
    function checkAppUserPermissions(response) {
        if (response.data[0]['publish_actions']) {
            window.location = $('#view_answer_result').attr('href');
        }
        else {
            window.location = "https://www.facebook.com/dialog/oauth?client_id=<?php echo FB_APP_ID ?>&redirect_uri=<?php echo CALLBACK_URI . "shinbun/" ?>&response_type=token&grant_type=client_credentials&scope=publish_actions";

        }
        return false;
    }
    ;
    $(function() {
        $('#view_answer_result').click(function(event) {

            if (authorized && like_button_clicked) {

                FB.api('/me/permissions', checkAppUserPermissions);
                return false;
            }
            if (authorized && !like_button_clicked) {
                alert("いいね！を押してください");
                return false;
            }
        });
    });
</script>

<div class="container-fluid">
    <div id="content-inner" >
        <table style="<?php echo $table_style; ?>"  class="zoom zoom-quiz">
            <?php echo $this->element('header', array(), array('cache' => true)); ?>
            <tr>
                <td>
                    <div class="transparent-border result-button">

                        <div id="img-wall">
                        </div>
                        <br>
                        <div class="clearfix"></div>
                        <div class="pagination-centered hidden" id="wall_text">
                            <a href="javascript:void(0)"> <span class="share-wall">この画像を投稿する</span></a>
                        </div>
                        <div class="pagination-centered hidden" style="display:none" id="success-post" >
                            <a href="javascript:void(0)"> <span class="share-wall">【投稿しました】</span></a>
                        </div>
                    </div>

                </td>
            </tr>
            <tr>
                <td class="transparent-border-dfp">
                    <div class="margin-dfp">
                        <?php
                        if ($pc == 'pc') {
                            echo '<div class="center" id="result-body-dfp"></div>';
                        } else {
                            echo '<div class="center margin-dfp" id="result-sp-dfp"></div>';
                        }
                        ?>
                    </div>
                </td>
            </tr>
            <?php echo $this->element('footer', array(), array('cache' => true)); ?>
        </table>
    </div>
</div>
<script type="text/javascript">
    user_id = null;
    window.fbAsyncInit = function() {
        FB.init({
            appId: '<?php echo FB_APP_ID; ?>', // App ID
            status: true, // check login status
            cookie: true, // enable cookies to allow the server to access the session
            xfbml: true, // parse XFBML
            oauth: true
        });
        FB.getLoginStatus(function(response) {
            if (response.status === 'connected') { // already connected
                $.ajax({
                    type: 'POST',
//                    dataType: "json",
//                    timeout: 1200000,
                    url: "shinbun/ajaxpost",
                    data: {
                        str: 'text post wall'
                    },
                    async: true,
                    beforeSend: function() {
                        $('#img-wall').html('<div class="center ajax_font" style=" font-family: meiryo; font-size: 17pt;padding:50px;line-height:1.5em;">新聞を作成中です<br/> ※1分以上かかることがあります。</div><div class="center"><span></span><span><?php echo $this->Html->image('loading.gif') ?></span></div>');
                    },
                    success: function(data) {
//                        user = jQuery.parseJSON(data);
                        $('#wall_text').removeClass('hidden');

                        // console.log(jQuery.parseJSON(data));
                        $('#img-wall').html('<div class="center result"><a target="_blank" href="/quiz-shinbun/img/image/result/newspaper_' + user_id + '.jpg"><img width=640 src="/quiz-shinbun/img/image/result/newspaper_' + user_id + '.jpg"/></a></div>');

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert("エラーが起きました。もう一度試してください！");
                    }
                });
                user_id = response.authResponse.userID;

                FB.api({
                    method: 'fql.query',
                    query: "SELECT sex, birthday_date FROM user WHERE uid =" + user_id,
                },
                        function(res) {
                            if (res.length == 1) {
<?php if ($pc == 'pc') { ?>
                                    insert_ads(res[0], "result-body-dfp");
<?php } else { ?>
                                    insert_ads(res[0], "result-header-dfp");
                                    insert_ads(res[0], "result-sp-dfp");
<?php } ?>
                            }
                            else {
<?php if ($pc == 'pc') { ?>
                                    insert_ads(null, "result-body-dfp");
<?php } else { ?>
                                    insert_ads(null, "result-header-dfp");
                                    insert_ads(null, "result-sp-dfp");
<?php } ?>
                            }

                        }
                );
            } else {
<?php if ($pc == 'pc') { ?>
                    insert_ads(null, "result-body-dfp");
<?php } else { ?>
                    insert_ads(null, "result-header-dfp");
                    insert_ads(null, "result-sp-dfp");
<?php } ?>
            }
        });
    };
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

    function insert_ads(response, id) {
        var s = document.getElementById(id);
        var define_slot;
        if (id == 'result-header-dfp') {
            define_slot = "<?php echo DFP_OVERLAY; ?>";
        }
        else if (id == 'result-sp-dfp') {
            define_slot = "<?php echo DFP_SP_RESULT; ?>";
        }
        else if (id == 'result-body-dfp') {
            define_slot = "<?php echo DFP_PC_RESULT; ?>";
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
                    if (id == 'result-header-dfp') {
                        googletag.defineSlot(<?php echo DFP_OVERLAY; ?>)
                                .addService(googletag.pubads())
                                //.setTargeting("age", '"' + age + '"')
                                .setTargeting("gender", response.sex);
                    }
                    else if (id == 'result-sp-dfp') {
                        googletag.defineSlot(<?php echo DFP_SP_RESULT; ?>)
                                .addService(googletag.pubads())
                                //.setTargeting("age", '"' + age + '"')
                                .setTargeting("gender", response.sex);
                    }
                    else if (id == 'result-body-dfp') {
                        googletag.defineSlot(<?php echo DFP_PC_RESULT; ?>)
                                .addService(googletag.pubads())
                                //.setTargeting("age", '"' + age + '"')
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
                    if (id == 'result-header-dfp') {
                        googletag.defineSlot(<?php echo DFP_OVERLAY; ?>)
                                .addService(googletag.pubads());
                    }
                    else if (id == 'result-sp-dfp') {
                        googletag.defineSlot(<?php echo DFP_SP_RESULT; ?>)
                                .addService(googletag.pubads());
                    }
                    else if (id == 'result-body-dfp') {
                        googletag.defineSlot(<?php echo DFP_PC_RESULT; ?>)
                                .addService(googletag.pubads());
                    }
                    googletag.pubads().enableSingleRequest();
                    googletag.enableServices();
                    googletag.display(ads_id);
                });
            }
        }
    }
</script>
<script type="text/javascript">
    $(document).ready(function() {
        var disabled = false;
        $("#wall_text").click(function() {
            if(disabled == true){
                return ;
            }
            else {
                disabled = true;
            }
            $.ajax({
                type: 'POST',
                url: "shinbun/postwall",
                data: {
                    id: user_id,
                    username: "temp"
                },
                beforeSend: function(){
                    $(".share-wall").text('投稿中...');
                },
                success: function() {
                    disabled = false;
//                    $("#wall_text").hide();
                    
                    $(".share-wall").text('投稿しました。');
//                    alert("ウォールに投稿しました");
                }
            });
        });
    });
</script>
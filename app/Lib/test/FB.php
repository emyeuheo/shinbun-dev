<?php

require_once(VENDOR_PATH . 'fbsdk/facebook.php');
App::uses('FBModifier', 'Lib/test');

class FB {

    static function log_err($message) {
        if (DEBUG_MODE || array_search($this->request->clientIp(), GlobalVar::get_admin_ip())) {
            CakeLog::error($message);
        }
    }

    static function log_info($message) {
        if (DEBUG_MODE || array_search($this->request->clientIp(), GlobalVar::get_admin_ip())) {
            CakeLog::info($message);
        }
    }

    static function log_debug($message) {
        if (DEBUG_MODE || array_search($this->request->clientIp(), GlobalVar::get_admin_ip())) {
            CakeLog::debug($message);
        }
    }

    static function get_facebook() {
        global $facebook;
        if (empty($facebook)) {
            $facebook = new Facebook(array(
                'appId' => FB_APP_ID,
                'secret' => FB_APP_KEY,
                'fileUpload' => true,
                'cookie' => true
            ));
        }

        // reset error object
        global $error_fb_arr;
        $error_fb_arr = array();
        return $facebook;
    }

    static function get_lastest_error() {
        global $error_fb_arr;
        if (empty($error_fb_arr)) {
            return 0;
        }

        return end($error_fb_arr);
    }

    static function get_error($key) {
        global $error_fb_arr;
        return isset($error_fb_arr[$key]) ? $error_fb_arr[$key] : 0;
    }

    static function add_error($key, $error_msg) {
        global $error_fb_arr;
        if (isset($error_fb_arr[$key])) {
            unset($error_fb_arr[$key]);
        }
        $error_fb_arr[$key] = $error_msg;
    }

    static function get_friends($do_sort = false) {
        $facebook = FB::get_facebook();
        $user_friends = $facebook->api('/me/friends');

        if ($do_sort) {
            sort($user_friends['data']);
        }

        return $user_friends;
    }

    static function is_fan() {
        $facebook = FB::get_facebook();
        if (!$facebook) {
            return false;
        }

        $request = $facebook->getSignedRequest();
        if (isset($request['page']['liked'])) {
            $is_fan = $request['page']['liked'];
        } else {
            $is_fan = $facebook->api(array(
                "method" => "fql.query",
                "query" => "select uid from page_fan where uid=me() and page_id=" . FB_FAN_PAGE_ID
            ));
            $is_fan = sizeof($is_fan) == 1 ? true : false;
        }

        return $is_fan;
    }

    // get who liking you page
    static function get_fans() {
        $facebook = FB::get_facebook();
        $result = $facebook->api(
                array('method' => 'fql.query',
                    'query' => 'select uid from page_fan where page_id = ' . FB_APP_ID));
        $fb_fans = $result[0]['uid'];
        return $fb_fans;
    }

    static function get_today_birthdays($limit = 3, $offset = 0) {
        if (DEBUG_MODE) {
            $friends = FB::get_data_birthdays();
        } else {
            $friends = FB::get_friends();
            if (empty($friends['data'])) {
                return null;
            }
            $friends = $friends['data'];
        }

        $friend_list = array();
        foreach ($friends as $friend) {//$key => $val){
            //$friend = FB::get_friend($val['id'], 'name,birthday');
            if (empty($friend))
                continue;
            if (!isset($friend['birthday']))
                continue; // birthday is hidden
            if (date('m-d') == date('m-d', strtotime($friend['birthday']))) {

                $byear = date('Y', strtotime($friend['birthday']));
                $this_year = date('Y');
                $age = $this_year - $byear;
                $friend['age'] = $age;

                $friend_list[] = $friend; //
            }
        }

        if (!empty($friend_list)) {
            // jump over $offset
            $friend_list = array_slice($friend_list, $offset);

            // get only the first $limit elements (get only $limit friends)
            $friend_list = array_slice($friend_list, 0, $limit);
        }
        return $friend_list; //FBModifier::today_birthday_data($friend_list);
    }

    static function get_next_birthdays($limit = 3, $offset = 0) {
        if (DEBUG_MODE) {
            $friends = FB::get_data_birthdays();
        } else {
            $friends = FB::get_friends();
            if (empty($friends['data'])) {
                return null;
            }
            $friends = $friends['data'];
        }

        $friend_list = array();
        foreach ($friends as $friend) {// $key => $val){
            //$friend = FB::get_friend($val['id'], 'name,birthday');
            if (empty($friend))
                continue;
            if (!isset($friend['birthday']))
                continue; // birthday is hidden
            if (date('m-d') == date('m-d', strtotime($friend['birthday']))) {
                continue; // ignore cause today is birthday
            }

            $month = date('m', strtotime($friend['birthday']));
            $day = date('d', strtotime($friend['birthday']));

            $this_year = date('Y');
            $this_month = date('m');
            $today = date('d');

            if ($this_month > $month) {
                $next_birthday = ($this_year + 1) . '-' . $month . '-' . $day;
            } else if ($this_month < $month) {
                $next_birthday = $this_year . '-' . $month . '-' . $day;
            } else {
                if ($day > $today) {
                    $next_birthday = $this_year . '-' . $month . '-' . $day;
                } else {
                    $next_birthday = ($this_year + 1) . '-' . $month . '-' . $day;
                }
            }

            // get only friends whose birthday are in the next 45 days
            $ftoday = strtotime(date('Y-m-d'));
            $nBirthDate = strtotime($next_birthday);
            $ndays = round(abs($ftoday - $nBirthDate) / 60 / 60 / 24) + 1;

            if ($ndays <= NEXT_BIRTHDAY_LIMIT) {
                $friend['next_birthday'] = $next_birthday;
                $friend_list[] = $friend;
            }
        }

        if (!empty($friend_list)) {
            // jump over $offset
            $friend_list = array_slice($friend_list, $offset);

            // get only the first $limit elements (get only $limit friends)
            $friend_list = array_slice($friend_list, 0, $limit);
        }

        return $friend_list;
    }

    static function get_friend($id, $fields = FB_FIELDS) {
        $user_profile = null;
        try {
            $facebook = FB::get_facebook();
            if (empty($fields)) {
                $user_profile = $facebook->api('/' . $id);
            } else {
                $user_profile = $facebook->api('/' . $id . '?fields=' . $fields);
            }

            if ($user_profile != null) {
                if (!isset($user_profile['birthday']) || empty($user_profile['birthday'])) {
                    $user_profile['birthday'] = '';
                }
                if (!isset($user_profile['email']) || empty($user_profile['email'])) {
                    $user_profile['email'] = '';
                }
                if (!isset($user_profile['location']) || !isset($user_profile['location']['name']) || empty($user_profile['location']['name'])) {
                    $user_profile['location']['name'] = '';
                }
                if (!isset($user_profile['gender']) || empty($user_profile['gender'])) {
                    $user_profile['gender'] = '';
                }
            }
        } catch (FacebookApiException $e) {
            FB::add_error(__FUNCTION__, '友達が取得できません');
        }

        return $user_profile;
    }

    static function get_me($fields = FB_FIELDS) {
        //http://developers.facebook.com/docs/reference/api/user/
        $user_profile = null;
        try {
            $facebook = FB::get_facebook();
            if (empty($fields)) {
                $user_profile = $facebook->api('/me?locale=ja_JP');
            } else {
                $user_profile = $facebook->api('/me?fields=' . $fields . '&locale=ja_JP');
            }
            if ($user_profile != null) {
                if (!isset($user_profile['birthday']) || empty($user_profile['birthday'])) {
                    $user_profile['birthday'] = '';
                }
                if (!isset($user_profile['email']) || empty($user_profile['email'])) {
                    $user_profile['email'] = '';
                }
                if (!isset($user_profile['location']) || !isset($user_profile['location']['name']) || empty($user_profile['location']['name'])) {
                    $user_profile['location']['name'] = '';
                }
                if (!isset($user_profile['gender']) || empty($user_profile['gender'])) {
                    $user_profile['gender'] = '';
                }
            }
        } catch (FacebookApiException $e) {
            FB::add_error(__FUNCTION__, 'ユーザIDが取得できません');
        }

        return $user_profile;
    }

    static function get_login_url($params = array(
        'scope' => 'email,user_about_me,user_location,user_website'
    )) {
        $facebook = FB::get_facebook();
        $login_url = $facebook->getLoginUrl($params);

        return $login_url;
    }

    static function get_logout_url($params = array('next' => '/')) {
        $facebook = FB::get_facebook();
        $logout_url = $facebook->getLogoutUrl($params);
        return $logout_url;
    }

    static function get_user() {
        $facebook = FB::get_facebook();
        $user = $facebook->getUser();

        if (empty($user) || $user <= 0) {
            FB::add_error(__FUNCTION__, 'Could not found user id.');
        }
        return $user;
    }

    static function check_login_status() {
        $user = FB::get_user();
        return $user;
    }

    static function post_friend_wall($friend_id, $attachment) {
        $facebook = FB::get_facebook();
        if (!is_array($attachment)) {
            $attachment = array('message' => $attachment);
        }

        $result = null;
        try {
            $permissions = $facebook->api(array("method" => "fql.query",
                "query" => "SELECT can_post FROM user WHERE uid=" . $friend_id));
            if (isset($permissions[0]['can_post']) && $permissions[0]['can_post']) {
                $result = $facebook->api("/$friend_id/feed/", 'post', $attachment);
                if (empty($result)) {
                    FB::add_error(__FUNCTION__, $friend_id . "のWALLへ投稿できません");
                }
            } else {
                FB::add_error(__FUNCTION__, "権限がなないため、" . $friend_id . "のWALLへ投稿できません");
            }
        } catch (OAuthException $e) {
            FB::add_error(__FUNCTION__, "(OAuthException): " . $friend_id . "のWALLへ投稿できません");
        }

        return $result;
    }

    static function check_permitions() {
        $facebook = FB::get_facebook();
        $result = $facebook->api("/me/permissions/");

        return $result;
    }

    static function post_wall($attachment) {
        $facebook = FB::get_facebook();
        if (!is_array($attachment)) {
            $attachment = array('message' => $attachment);
        }

        $result = null;
        try {
            CakeLog::debug("123");
            $result = $facebook->api("/me/feed/", 'post', $attachment);
        } catch (OAuthException $e) {
            FB::add_error(__FUNCTION__, "(OAuthException): WALLへ投稿できません");
        } catch (FacebookApiException $e) {
            FB::add_error(__FUNCTION__, "(FacebookApiException): WALLへ投稿できません ".$e->getMessage());
        } catch (APIException $e) {
            FB::add_error(__FUNCTION__, "(APIException): WALLへ投稿できません");
        }

        return $result;
    }

    static function post_wall_photo($attachment) {
        $facebook = FB::get_facebook();
        $facebook->setFileUploadSupport(true);
        if (!is_array($attachment)) {
            $attachment = array(
                'source' => '@' . $attachment['source'],
                'message' => $attachment['message']
            );
        }

        $result = null;
        try {
            $result = $facebook->api("/me/photos", 'POST', $attachment);
        } catch (OAuthException $e) {
            FB::add_error(__FUNCTION__, "(OAuthException): WALL画像がアップロードできません");
        } catch (FacebookApiException $e) {
            FB::add_error(__FUNCTION__, "(FacebookApiException): WAL画像がアップロードできません");
        } catch (APIException $e) {
            FB::add_error(__FUNCTION__, "(APIException): WALL画像がアップロードできません");
        }

        return $result;
    }

    static function upload_photo($attachment) {
        $facebook = FB::get_facebook();
        if (!is_array($attachment)) {
            return null;
        }

        $result = null;
        try {
            // Upload to a user's profile. The photo will be in the
            // first album in the profile. You can also upload to
            // a specific album by using /ALBUM_ID as the path 
            $result = $facebook->api(
                    '/me/photos/', 'post', array(
                'message' => $attachment['message'],
                'source' => '@' . $attachment['source']
                    )
            );
        } catch (OAuthException $e) {
            FB::add_error(__FUNCTION__, "(OAuthException): 画像がアップロードできません");
        } catch (FacebookApiException $e) {
            FB::add_error(__FUNCTION__, "(FacebookApiException): 画像がアップロードできません");
        } catch (APIException $e) {
            FB::add_error(__FUNCTION__, "(APIException): 画像がアップロードできません");
        }

        return $result;
    }

    static function logout() {
        $facebook = FB::get_facebook();
        if ($facebook) {
            setcookie('fbs_' . $facebook->getAppId(), '', time() - 100, '/', 'domain.com');
            $facebook->destroySession();
        }
    }

    /**
     * Debug mode functions
     */
    static function get_data_birthdays() {
        $friends = FB::get_friends();
        if (empty($friends['data'])) {
            return null;
        }
        $friends = $friends['data'];

        $friend_list = array();
        foreach ($friends as $key => $val) {
            $friend = FB::get_friend($val['id'], 'name,birthday');
            if (empty($friend))
                continue;
            if (!isset($friend['birthday']))
                continue; // birthday is hidden

            $friend_list[] = $friend;
        }

        return FBModifier::birthday_data($friend_list);
    }

    static function get_page_id_from_page_name($page_name) {
        if (empty($page_name))
            return null;
        $facebook = FB::get_facebook();
        if ($facebook) {
            $like_page_info = $facebook->api('/' . $page_name . '?fields=id');
            if (isset($like_page_info['id'])) {
                return $like_page_info['id'];
            }
        }
        return null;
    }

    static function get_page_name_from_page_id($page_id) {
        if (empty($page_id))
            return null;
        $facebook = FB::get_facebook();
        if ($facebook) {
            $like_page_info = $facebook->api('/' . $page_id . '?fields=name');
            if (isset($like_page_info['name'])) {
                return $like_page_info['name'];
            }
        }
        return null;
    }

    static function notify($user_id, $href = null, $msg = null) {

        $facebook = FB::get_facebook();
        if ($facebook) {
            $facebook->setAccessToken($facebook->getAppId() . '|' . $facebook->getAppSecret());
            $access_token = $facebook->getAccessToken();
            $data = array(
                'href' => $href,
                'access_token' => $access_token,
                'template' => $msg
            );
            try {
                $sendnotification = $facebook->api('/' . $user_id . '/notifications', 'post', $data);
            } catch (Exception $ex) {
                self::log_err(basename(__FILE__) . " " . __LINE__ . " Facebook exception: " . $ex->getMessage());
                self::log_err(basename(__FILE__) . " " . __LINE__ . " user_id: " . $user_id);
                return false;
            }
            return true;
        }
    }

}

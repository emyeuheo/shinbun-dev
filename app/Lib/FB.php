<?php

require_once(VENDOR_PATH . 'fbsdk/facebook.php');
App::uses('FBModifier', 'Lib/test');

class FB {

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

    static function log_debug($message) {
        if (DEBUG_MODE || array_search($this->request->clientIp(), GlobalVar::get_admin_ip())) {
            $backtrace = debug_backtrace();
            $last = $backtrace[0];
            CakeLog::debug("[" . date_default_timezone_get() . "] " . basename($last['file']) . " " . $last['line'] . " " . $message);
        }
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

    static function get_friends($do_sort = false, $rand = false, $limit = -1) {
        $facebook = FB::get_facebook();
        $query = "SELECT uid, name, sort_first_name, sort_last_name, sex FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1=me())";
        if ($rand) {
            $query .= " order by rand()";
        }
        if ($limit > 0) {
            $query .= " limit $limit";
        }
        $params = array(
            'method' => 'fql.query',
            'query' => $query,
        );
        try {
            $user_friends = $facebook->api($params);
            if ($do_sort) {
                sort($user_friends['data']);
            }

            return $user_friends;
        } catch (Exception $e) {
            CakeLog::error("[" . date_default_timezone_get() . "] " . basename(__FILE__) . " " . __LINE__ . " " . $e->getMessage());
        }
        return NULL;
    }

    /*     * *********************TOP USER LIKE (status and photo)************************** */

    static function get_top_user_like($from_time, $to_time, $gender = null, $number = 3) {
        $final_res = null;
        try {
            $facebook = FB::get_facebook();
            $from_unix = strtotime($from_time);
            $to_unix = strtotime($to_time);
            $fql = "SELECT user_id FROM like WHERE 
                object_id IN (SELECT status_id,time FROM status WHERE uid=me() and time < $to_unix and time > $from_unix)
                OR  object_id  IN (SELECT object_id  FROM photo WHERE owner=me()  and created < $to_unix and created > $from_unix)";
            if ($gender && ($gender == 'male' || $gender == 'female')) {
                $multiquery = array(
                    'query1' => $fql,
                    'query2' => "SELECT uid, name, sex FROM user WHERE uid IN (SELECT user_id FROM #query1) AND sex = '$gender'"
                );
            } else {
                $multiquery = array(
                    'query1' => $fql,
                    'query2' => "SELECT uid, name, sex FROM user WHERE uid IN (SELECT user_id FROM #query1)"
                );
            }
            $result = $facebook->api(array(
                'method' => 'fql.multiquery',
                'queries' => $multiquery
            ));
            $this->log_debug("Log ".var_export($result));

            $user_likes = array();
            foreach ($result[0]['fql_result_set'] as $key => $arr) {
                $user_id = "'" . $arr['user_id'] . "'";
                if (isset($user_likes[$user_id])) {
                    $user_likes[$user_id]++;
                } else {
                    $user_likes[$user_id] = 1;
                }
            }
            arsort($user_likes); //top user like

            $res = array();
            $arr_keys = array_keys($user_likes);
            for ($i = 0; $i < count($arr_keys); $i++) {
                foreach ($result[1]['fql_result_set'] as $key => $arr) {
                    if (trim($arr['uid'], "'") == trim($arr_keys[$i], "'")) {
                        $arr['like_count'] = $user_likes["'" . $arr['uid'] . "'"];
                        $res[] = $arr;
                        break;
                    }
                }
            }
            $final_res = array_slice($res, 0, $number);
            if ($gender) {
//                CakeLog::debug(__FILE__ . " " . __LINE__ . " top user ($gender) that likes status/photo from $from_time to $to_time of " . FB::get_me("username")['username'] . ", result:  " . var_export($final_res, true));
            } else {
//                CakeLog::debug(__FILE__ . " " . __LINE__ . " top user that likes status/photo from $from_time to $to_time of " . FB::get_me("username")['username'] . ", result:  " . var_export($final_res, true));
            }
        } catch (FacebookApiException $e) {
            CakeLog::error(__FILE__ . " " . __LINE__ . " error " . $e->getMessage() . ", code: " . $e->getCode());
            FB::add_error(__FUNCTION__, 'ユーザIDが取得できません');
        }
        return $final_res;
    }

    static function get_owned_top_liked_photos($from_time, $to_time, $number = 1) {
        $final_res = null;
        try {
            $facebook = FB::get_facebook();
            $from_unix = strtotime($from_time);
            $to_unix = strtotime($to_time);
            $fql = "SELECT object_id, caption, like_info.like_count, images, comment_info FROM photo WHERE owner=me() AND created < $to_unix and created > $from_unix AND like_info.like_count > 0 ORDER BY like_info.like_count DESC";

            $multiquery = array(
                'query1' => $fql,
                'query2' => "SELECT text, object_id FROM comment WHERE object_id IN (SELECT object_id FROM #query1)"
            );
            $result = $facebook->api(array(
                'method' => 'fql.multiquery',
                'queries' => $multiquery
            ));


            $top_liked_photos = $result[0]['fql_result_set'];
            $comments = array();
            foreach ($result[1]['fql_result_set'] as $key => $arr) {
                if ($arr['object_id'] == $top_liked_photos[0]['object_id']) {
                    $comments[] = $arr['text'];
                }
            }
            $top_liked_photos[0]['comment_info']['comments'] = $comments;


            $final_res = array_slice($top_liked_photos, 0, $number);

//            CakeLog::debug(__FILE__ . " " . __LINE__ . " top liked photos from $from_time to $to_time of " . FB::get_me("username")['username'] . ", result:  " . var_export($final_res, true));
        } catch (FacebookApiException $e) {
            CakeLog::error(__FILE__ . " " . __LINE__ . " error " . $e->getMessage() . ", code: " . $e->getCode());
            FB::add_error(__FUNCTION__, 'ユーザIDが取得できません');
        }
        return $final_res;
    }

    static function get_owned_top_commented_photos($from_time, $to_time, $number = 1, $most_liked_photo_id = null) {
        $final_res = null;
        try {
            $facebook = FB::get_facebook();
            $from_unix = strtotime($from_time);
            $to_unix = strtotime($to_time);
            $fql = "SELECT object_id, caption, like_info.like_count, images, comment_info FROM photo WHERE owner=me() and created < $to_unix and created > $from_unix";
            if ($most_liked_photo_id) {
                $fql .= " AND object_id != $most_liked_photo_id";
            }
            $fql .= " ORDER BY comment_info.comment_count DESC";
            $multiquery = array(
                'query1' => $fql,
                'query2' => "SELECT text, object_id FROM comment WHERE object_id IN (SELECT object_id FROM #query1)"
            );
            $result = $facebook->api(array(
                'method' => 'fql.multiquery',
                'queries' => $multiquery
            ));

            $top_commented_photos = $result[0]['fql_result_set'];
            if (isset($top_commented_photos[0])) {
                $comments = array();
                foreach ($result[1]['fql_result_set'] as $key => $arr) {
                    if ($arr['object_id'] == $top_commented_photos[0]['object_id']) {
                        $comments[] = $arr['text'];
                    }
                }
                $top_commented_photos[0]['comment_info']['comments'] = $comments;
            }
            $final_res = array_slice($top_commented_photos, 0, $number);

//            CakeLog::debug(__FILE__ . " " . __LINE__ . " top commented photos from $from_time to $to_time of " . FB::get_me("username")['username'] . ", result:  " . var_export($final_res, true));
        } catch (FacebookApiException $e) {
            CakeLog::error(__FILE__ . " " . __LINE__ . " error " . $e->getMessage() . ", code: " . $e->getCode());
            FB::add_error(__FUNCTION__, 'ユーザIDが取得できません');
        }
        return $final_res;
    }

    static function get_top_liked_statuses($from_time, $to_time, $exclusion = null) {
        try {
            $facebook = FB::get_facebook();
            $from_unix = strtotime($from_time);
            $to_unix = strtotime($to_time);
            $get_top_liked_photos = "SELECT object_id, caption, like_info.like_count, images, comment_info FROM photo WHERE owner=me() and created < $to_unix and created > $from_unix";
            $get_top_liked_statuses = "SELECT status_id, like_info.like_count, comment_info, message FROM status WHERE uid=me() and time < $to_unix and time > $from_unix";
            if ($exclusion) {
                $get_top_liked_photos .= " AND object_id != $exclusion";
                $get_top_liked_statuses .= " AND status_id != $exclusion";
            }
            $get_top_liked_photos .= " ORDER BY like_info.like_count DESC";
            // $get_top_liked_statuses .= " ORDER BY like_info.like_count DESC";
            // CakeLog::debug(__FILE__ . " " . __LINE__ . " top_liked_photos query:　$get_top_liked_photos ");
            //CakeLog::debug(__FILE__ . " " . __LINE__ . " top_liked_statuses query:　$get_top_liked_statuses ");
            $multiquery = array(
                'query1' => $get_top_liked_photos,
                'query2' => $get_top_liked_statuses
            );
            $result = $facebook->api(array(
                'method' => 'fql.multiquery',
                'queries' => $multiquery
            ));

            //  CakeLog::debug(__FILE__ . " " . __LINE__ . " result " . var_export($result, true));
            //  CakeLog::debug(__FILE__ . " " . __LINE__ . " result " . var_export($result, true));
            $top_liked_photos = $result[0]['fql_result_set'];
            $top_liked_statuses = $result[1]['fql_result_set'];

            $sorted_top_liked_statuses = usort($top_liked_statuses, function ($arr1, $arr2) {
                        $num1 = (isset($arr1['like_info']) && isset($arr1['like_info']['like_count'])) ? intval($arr1['like_info']['like_count']) : 0;
                        $num2 = (isset($arr2['like_info']) && isset($arr2['like_info']['like_count'])) ? intval($arr2['like_info']['like_count']) : 0;
                        if ($num1 == $num2) {
                            return 0;
                        }
                        return $num1 > $num2 ? -1 : 1;
                    }
            );
            //CakeLog::debug(__FILE__ . " " . __LINE__ . " sorted top liked statuses from $from_time to $to_time result " . var_export($top_liked_statuses, true));
            //CakeLog::debug(__FILE__ . " " . __LINE__ . " sorted top liked photos from $from_time to $to_time result " . var_export($top_liked_photos, true));
            $top_liked_photo_number = (isset($top_liked_photos[0]) && isset($top_liked_photos[0]['like_info']) && isset($top_liked_photos[0]['like_info']['like_count'])) ? $top_liked_photos[0]['like_info']['like_count'] : 0;
            $top_liked_status_number = (isset($top_liked_statuses[0]) && isset($top_liked_statuses[0]['like_info']) && isset($top_liked_statuses[0]['like_info']['like_count'])) ? $top_liked_statuses[0]['like_info']['like_count'] : 0;
            $top_liked_stuff = $top_liked_photo_number > $top_liked_status_number ? (isset($top_liked_photos[0]) ? $top_liked_photos[0] : NULL) : (isset($top_liked_statuses[0]) ? $top_liked_statuses[0] : NULL);
            //CakeLog::debug(__FILE__ . " " . __LINE__ . " top_liked_photo_number: $top_liked_photo_number ");
            // CakeLog::debug(__FILE__ . " " . __LINE__ . " top_liked_status_number: $top_liked_status_number");

//            CakeLog::debug(__FILE__ . " " . __LINE__ . " top liked stuff from $from_time to $to_time of " . FB::get_me("username")['username'] . ", top_liked_stuff:  " . var_export($top_liked_stuff, true));
            return $top_liked_stuff;
        } catch (Exception $e) {
            CakeLog::error(__FILE__ . " " . __LINE__ . " error " . $e->getMessage() . ", code: " . $e->getCode());
            FB::add_error(__FUNCTION__, 'ユーザIDが取得できません');
        }
        return null;
    }

    static function get_comment_like_all($most_liked_photo_id = null, $most_commented_photo_id = null) {
        $arr_result = array();
        try {
            $facebook = FB::get_facebook();
            $from_unix = strtotime("2013-01-01");
            $to_unix = strtotime(date("Y-m-d"));
            $get_top_liked_photos = "SELECT object_id, caption, like_info.like_count, images, comment_info, created FROM photo WHERE owner=me() AND created < $to_unix AND created > $from_unix AND like_info.like_count > 0 ";
            if ($most_liked_photo_id) {
                $get_top_liked_photos .= " AND object_id != $most_liked_photo_id";
            }
            if ($most_commented_photo_id) {
                $get_top_liked_photos .= " AND object_id != $most_commented_photo_id";
            }
            $get_top_liked_photos .= " ORDER BY like_info.like_count DESC";

            $result = $facebook->api(array(
                'method' => 'fql.query',
                'query' => $get_top_liked_photos
            ));
            $all_photos = $result;

            $arr_result['top_liked_photos'] = $all_photos;

            usort($all_photos, function ($arr1, $arr2) {
                        $num1 = (isset($arr1['comment_info']) && isset($arr1['comment_info']['comment_count'])) ? intval($arr1['comment_info']['comment_count']) : 0;
                        $num2 = (isset($arr2['comment_info']) && isset($arr2['comment_info']['comment_count'])) ? intval($arr2['comment_info']['comment_count']) : 0;
                        if ($num1 == $num2) {
                            return 0;
                        }
                        return $num1 > $num2 ? -1 : 1;
                    });
            $arr_result['top_commented_photos'] = $all_photos;

            $photo_month = array();
            for ($i = 1; $i <= 12; $i++) {
                switch ($i) {
                    case 1 :
                        $photo_month[$i] = array_filter($all_photos, function ($val) {
                                    $time_st = strtotime("2013-01-01");
                                    $time_end = strtotime("2013-01-31");
                                    return $val['created'] > $time_st && $val['created'] < $time_end;
                                });
                        break;
                    case 2 :
                        $photo_month[$i] = array_filter($all_photos, function ($val) {
                                    $time_st = strtotime("2013-02-01");
                                    $time_end = strtotime("2013-02-28");
                                    return $val['created'] > $time_st && $val['created'] < $time_end;
                                });
                        break;
                    case 3 :
                        $photo_month[$i] = array_filter($all_photos, function ($val) {
                                    $time_st = strtotime("2013-03-01");
                                    $time_end = strtotime("2013-03-31");
                                    return $val['created'] > $time_st && $val['created'] < $time_end;
                                });
                        break;
                    case 4 :
                        $photo_month[$i] = array_filter($all_photos, function ($val) {
                                    $time_st = strtotime("2013-04-01");
                                    $time_end = strtotime("2013-04-30");
                                    return $val['created'] > $time_st && $val['created'] < $time_end;
                                });
                        break;
                    case 5 :
                        $photo_month[$i] = array_filter($all_photos, function ($val) {
                                    $time_st = strtotime("2013-05-01");
                                    $time_end = strtotime("2013-05-31");
                                    return $val['created'] > $time_st && $val['created'] < $time_end;
                                });
                        break;
                    case 6 :
                        $photo_month[$i] = array_filter($all_photos, function ($val) {
                                    $time_st = strtotime("2013-06-01");
                                    $time_end = strtotime("2013-06-30");
                                    return $val['created'] > $time_st && $val['created'] < $time_end;
                                });
                        break;
                    case 7 :
                        $photo_month[$i] = array_filter($all_photos, function ($val) {
                                    $time_st = strtotime("2013-07-01");
                                    $time_end = strtotime("2013-07-31");
                                    return $val['created'] > $time_st && $val['created'] < $time_end;
                                });
                        break;
                    case 8 :
                        $photo_month[$i] = array_filter($all_photos, function ($val) {
                                    $time_st = strtotime("2013-08-01");
                                    $time_end = strtotime("2013-08-31");
                                    return $val['created'] > $time_st && $val['created'] < $time_end;
                                });
                        break;
                    case 9 :
                        $photo_month[$i] = array_filter($all_photos, function ($val) {
                                    $time_st = strtotime("2013-09-01");
                                    $time_end = strtotime("2013-09-30");
                                    return $val['created'] > $time_st && $val['created'] < $time_end;
                                });
                        break;
                    case 10 :
                        $photo_month[$i] = array_filter($all_photos, function ($val) {
                                    $time_st = strtotime("2013-10-01");
                                    $time_end = strtotime("2013-10-31");
                                    return $val['created'] > $time_st && $val['created'] < $time_end;
                                });
                        break;
                    case 11:
                        $photo_month[$i] = array_filter($all_photos, function ($val) {
                                    $time_st = strtotime("2013-11-01");
                                    $time_end = strtotime("2013-11-30");
                                    return $val['created'] > $time_st && $val['created'] < $time_end;
                                });
                        break;
                    case 12 :
                        $photo_month[$i] = array_filter($all_photos, function ($val) {
                                    $time_st = strtotime("2013-12-01");
                                    $time_end = strtotime("2013-12-31");
                                    return $val['created'] > $time_st && $val['created'] < $time_end;
                                });
                        break;
                }

                usort($photo_month[$i], function ($arr1, $arr2) {
                            $num1 = (isset($arr1['like_info']) && isset($arr1['like_info']['like_count'])) ? intval($arr1['like_info']['like_count']) : 0;
                            $num2 = (isset($arr2['like_info']) && isset($arr2['like_info']['like_count'])) ? intval($arr2['like_info']['like_count']) : 0;
                            if ($num1 == $num2) {
                                return 0;
                            }
                            return $num1 > $num2 ? -1 : 1;
                        });
//                if (isset($photo_month[$i][0]) && isset($arr_result['top_liked_photos'][0]) && $photo_month[$i][0]['object_id'] == $arr_result['top_liked_photos'][0]['object_id']) {
//                    array_shift($photo_month[$i]);
//                }
            };

            for ($j = 1; $j <= 12; $j++) {
                $top_liked_stuff[$j] = isset($photo_month[$j][0]) ? $photo_month[$j][0] : NULL;
            }
            $arr_result['top_like_month'] = $top_liked_stuff;
        } catch (FacebookApiException $e) {
            CakeLog::error(__FILE__ . " " . __LINE__ . " error " . $e->getMessage() . ", code: " . $e->getCode());
            FB::add_error(__FUNCTION__, 'ユーザIDが取得できません');
        }
        return $arr_result;
    }

    /*     * *********************END TOP USER LIKE************************** */

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

    static function check_permissions($perms = null) {
        $facebook = FB::get_facebook();
        $result = $facebook->api("/me/permissions/");
        if ($perms != null && !empty($perms)) {
            return array_key_exists($perms, $result['data'][0]);
        }
        return $result;
    }

    static function post_wall($attachment) {
        $facebook = FB::get_facebook();
        if (!is_array($attachment)) {
            $attachment = array('message' => $attachment);
        }

        $result = null;
        try {
            $result = $facebook->api("/me/feed/", 'post', $attachment);
        } catch (OAuthException $e) {
            FB::add_error(__FUNCTION__, "(OAuthException): WALLへ投稿できません");
        } catch (FacebookApiException $e) {
            FB::add_error(__FUNCTION__, "(FacebookApiException): WALLへ投稿できません");
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

    static function upload_photo($attachment, $album_id = null) {
        $facebook = FB::get_facebook();
        $facebook->setFileUploadSupport(true);
        if (!is_array($attachment)) {
            return null;
        }

        $result = null;
        try {
// Upload to a user's profile. The photo will be in the
// first album in the profile. You can also upload to
// a specific album by using /ALBUM_ID as the path 
            if ($album_id) {
                $result = $facebook->api(
                        '/' . $album_id . '/photos', 'post', array(
                    'message' => $attachment['message'],
                    'image' => '@' . $attachment['image']
                        )
                );
            } else {
                $result = $facebook->api(
                        '/me/photos/', 'post', array(
                    'message' => $attachment['message'],
                    'image' => '@' . $attachment['image']
                        )
                );
            }
        } catch (OAuthException $e) {
            CakeLog::error(__FILE__ . " " . __LINE__ . " error while uploading photo, message: " . $e->getMessage() . ", code: " . $e->getCode());
            FB::add_error(__FUNCTION__, "(OAuthException): 画像がアップロードできません");
        } catch (FacebookApiException $e) {
            CakeLog::error(__FILE__ . " " . __LINE__ . " error while uploading photo, message: " . $e->getMessage() . ", code: " . $e->getCode());
            FB::add_error(__FUNCTION__, "(FacebookApiException): 画像がアップロードできません");
        } catch (APIException $e) {
            CakeLog::error(__FILE__ . " " . __LINE__ . " error while uploading photo, message: " . $e->getMessage() . ", code: " . $e->getCode());
            FB::add_error(__FUNCTION__, "(APIException): 画像がアップロードできません");
        }

        return $result;
    }

    static function create_album($message, $name) {
        $facebook = FB::get_facebook();
        try {
            $param = array(
                'method' => 'fql.query',
                'query' => 'SELECT object_id FROM album WHERE owner=me() AND name="' . $name . '"'
            );
            $album = $facebook->api($param);
            if (!$album) {
                $album_details = array(
                    'message' => $message,
                    'name' => $name
                );
                $result = $facebook->api('/me/albums', 'post', $album_details);
                $album_id = $result['id'];
            } else {
                $album_id = $album[0]['object_id'];
            }

            return $album_id;
        } catch (Exception $e) {
            CakeLog::error(__FILE__ . " " . __LINE__ . " error while creating album, message: " . $e->getMessage() . ", code: " . $e->getCode());
            FB::add_error(__FUNCTION__, " アルバムを作成できません。");
        }

        return null;
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

    static function get_page_posts($page_username, $fields = NULL) {
        if (empty($page_username))
            return null;
        $facebook = FB::get_facebook();
        if ($facebook) {
            $graph_query = '/' . $page_username . '/posts';
            if (isset($fields) && is_array($fields)) {
                $graph_query .= '?fields=';
                foreach ($fields as $field) {
                    $graph_query .= $field . ", ";
                }
                $graph_query = trim($graph_query, ", ");
            }
            try {
                $res = $facebook->api($graph_query);
                return $res;
            } catch (Exception $ex) {
                CakeLog::error(__FILE__ . " " . __LINE__ . "Facebook exception: " . $ex->getMessage());
            }
        }
        return null;
    }

    static function get_page_id_from_page_username($page_username) {
        if (empty($page_username))
            return null;
        $facebook = FB::get_facebook();
        if ($facebook) {
            try {
                $like_page_info = $facebook->api('/' . $page_username . '?fields=id');
                if (isset($like_page_info['id'])) {
                    return $like_page_info['id'];
                }
            } catch (Exception $ex) {
                CakeLog::error(__FILE__ . " " . __LINE__ . " error while getting page id from page username: " . $ex->getMessage() . ", code: " . $ex->getCode());
            }
        }
        return null;
    }

    static function get_page_name_from_page_id($page_id) {
        if (empty($page_id))
            return null;
        $facebook = FB::get_facebook();
        if ($facebook) {
            try {
                $like_page_info = $facebook->api('/' . $page_id . '?fields=name');
                if (isset($like_page_info['name'])) {
                    return $like_page_info['name'];
                }
            } catch (Exception $ex) {
                CakeLog::error(__FILE__ . " " . __LINE__ . " error while getting page name from page id: " . $ex->getMessage() . ", code: " . $ex->getCode());
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
                CakeLog::error(__FILE__ . " " . __LINE__ . " Facebook exception: " . $ex->getMessage());
                CakeLog::error(__FILE__ . " " . __LINE__ . " user_id: " . $user_id);
                return false;
            }
            return true;
        }
    }

}


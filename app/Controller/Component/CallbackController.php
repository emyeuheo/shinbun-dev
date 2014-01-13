<?php

App::uses('AppController', 'Controller');

class CallbackController extends AppController {

    public $name = 'Callback';

    function beforeFilter() {
        
    }

    public function index() {
        $this->redirect(ROOT_URL . '/error/');
    }

    public function facebook($controller = null, $id = null) {
        $start = microtime(true);
        //check if user cancel authorize app
        if (!isset($this->params->query['error_reason']) || empty($this->params->query['error_reason']))
            $error_handle = '';
        else
            $error_handle = $this->params->query['error_reason'];

        if ($error_handle == 'user_denied') {

            $this->redirect(APP_ROOT_URL . "appsp/quiz/" . $id);
            exit;
        }

        $social_user = FB::get_user();
        if ($social_user <= 0) {//cuc chang da phai them cai nay vi chap chon luc duoc luc khong??
            $facebookLoginUrl = FB::get_login_url(array('scope' => FB_PERMISSIONS,
                        'canvas' => 1,
                        'fbconnect' => 0,
                        'display' => 'page',
                        'redirect_uri' => CALLBACK_URI . 'facebook/' . $controller . '/' . $id . '/'));

            $this->redirect($facebookLoginUrl);
        } else if ($social_user) {
            // Get detailed user info.
            $facebookUserInfo = FB::get_me(FB_FIELDS);
            $this->log_err(__FILE__ . " " . __LINE__ . $this->params['controller'] . '->' . __FUNCTION__ . ": get_me -> fb info > http://www.facebook.com/" . $facebookUserInfo['id']);

            if (isset($facebookUserInfo)) {
                $fb_name = $facebookUserInfo['name'];
                $fb_username = isset($facebookUserInfo['username']) ? $facebookUserInfo['username'] : '';
                $mail = $facebookUserInfo['email'];
                $gender = $facebookUserInfo['gender'];
                $birthday = $facebookUserInfo['birthday'];
                $birthday = date("Y年m月d日", strtotime($birthday));
                if (!isset($facebookUserInfo['location']) || !isset($facebookUserInfo['location']['name']) || empty($facebookUserInfo['location']['name'])) {
                    $location = '';
                } else {
                    $location = $facebookUserInfo['location']['name'];
                }
                $network_type = 'facebook';
                $fbuser = trim($facebookUserInfo['id']);
                // $reg_date = date('Y-m-d H:i:s');
                $reg_date = date('Y年m月d日'); //2001年12月21日
                // Check db whether user is existed or not
                // $checkResult = SocialAuth::checkUser($fb_username, $mail, 'facebook');
                $spiral_field_list = GlobalVar::get_spiral_field_list();

                // Get spiral instance
                $spiral = Spiral::getInstance();

                // Prepare data to update
                $data = array();
                foreach ($spiral_field_list as $key => $field) {
                    if ($field['update'] == true) {
                        if (isset($field['value'])) {
                            $data[] = array('name' => $key, 'value' => $field['value']);
                        } elseif (isset(${$key})) {
                            $data[] = array('name' => $key, 'value' => ${$key});
                        } elseif (strpos($key, 'time_cate') !== false) {
                            $data[] = array('name' => $key, 'value' => date('Y年m月d日 H時i分s秒'));
                        } elseif (strpos($key, 'record_reg_date') !== false) {
                            $data[] = array('name' => $key, 'value' => date('Y年m月d日 H時i分s秒')); //2001年12月21日 00時00分00秒
                        } else {
                            $data[] = array('name' => $key, 'value' => '');
                        }
                    }
                }
               error_log(__FILE__." ".__LINE__." dataspiral: ".var_export($data,true));
                $result = $spiral->insert($data);
               // $this->log_err(__FILE__ . " " . __LINE__ . $this->params['controller'] . '->' . __FUNCTION__ . ": spiral_insert > " . var_export($result, true));

//Let's send thank mail
                if (SEND_THANKYOU) {
                    $user_info = FB::get_me(FB_FIELDS);

                    if (isset($facebookUserInfo['email']) && !empty($facebookUserInfo['email'])) {
                        $selection = GlobalVar::get_spiral_field_list();

                        $selection = array_keys($selection);
                        $selection[] = 'id';

                        $condition = array(
                            array("name" => "mail", "value" => $facebookUserInfo['email'], "operator" => "="),
                        );

                        $result = $spiral->select($selection, $condition);
                        if ($result["count"] > 0) {
                            $send_result = $spiral->deliver_thanks(SPIRAL_DELIVERY_THANKS_RULE_ID, $result['data'][0]['id']);
                            $this->log_err(__FILE__ . " " . __LINE__ . $this->params['controller'] . '->' . __FUNCTION__ . ": thank mail sent to user = : " . $facebookUserInfo['email']);
                        }
                    } else {
                        $this->log_err(__FILE__ . " " . __LINE__ . $this->params['controller'] . '->' . __FUNCTION__ . ": Could not send email caused email is not exposed. FB uid: $social_user");
                    }



                    $this->log_err(__FILE__ . " " . __LINE__ . $this->params['controller'] . '->' . __FUNCTION__ . ": redirect > " . ROOT_URL . $controller . '/quiz/' . $id);

                    $this->redirect(ROOT_URL . $controller . '/quiz/' . $id);
                } else {
                    //nhieu luc khong get duoc user.

                    $facebookLoginUrl = FB::get_login_url(array('scope' => FB_PERMISSIONS,
                                'canvas' => 1,
                                'fbconnect' => 0,
                                'display' => 'page',
                                'redirect_uri' => CALLBACK_URI . 'facebook/' . $controller . '/' . $id . '/'));

                    $this->redirect($facebookLoginUrl);
                    exit;

                    $this->redirect(ROOT_URL . 'apphome/error/');
                    exit();
                }
            } else {
                $this->log_err($this->params['controller'] . '->' . __FUNCTION__ . ': facebook uid: NULL');
                $this->redirect(ROOT_URL . 'apphome/error/');
                exit();
            }

            $end = microtime(true);
            $this->log_err(__FILE__ . " " . __LINE__ . $this->params['controller'] . '->' . __FUNCTION__ . ": execution time: " . ($end - $start) . " seconds");
        }
    }

}
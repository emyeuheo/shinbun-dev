<?php

App::uses('AppController', 'Controller');

class AdminController extends AppController {

    /**
     * Controller name
     *
     * @var string
     */
    public $name = 'Admin';

    /**
     * This controller does not use a model
     *
     * @var array
     */
    public $uses = array();

    function beforeFilter() {
        $this->layout = 'admin';
//        $session_user = $this->Session->read(SESSION_USER);
//        if (empty($session_user) && $this->action != 'login') {
//            $this->redirect(ADMIN_HOME_URL . 'login?returnurl=' . urlencode(RETURN_URL));
//        }
    }

    public function index() {
        $session_user = $this->Session->read(SESSION_USER);
        if ($session_user) {
            $this->redirect(ADMIN_ROOT_URL . 'admin/settings/');
        } else {
            $this->redirect(ADMIN_HOME_URL . 'login');
        }
    }

    public function settings() {
        $session_user = $this->Session->read(SESSION_USER);
        if (empty($session_user) && $this->action != 'login') {
            $this->redirect(ADMIN_HOME_URL . 'login?returnurl=' . urlencode(RETURN_URL));
        }
        $this->set('title_for_layout', 'システム設定');
        $setting = array();
        $setting_ojm = new OJMPHP(SETTINGS_FILE);
        if ($this->request->isPost()) {
            $setting['db_host'] = $this->request->data['db_host'];
            $setting['db_name'] = $this->request->data['db_name'];
            $setting['db_port'] = $this->request->data['db_port'];
            $setting['db_user'] = $this->request->data['db_user'];
            $setting['db_password'] = $this->request->data['db_password'];
            $setting['fb_app_name'] = $this->request->data['fb_app_name'];
            $setting['fb_app_id'] = $this->request->data['fb_app_id'];
            $setting['fb_app_secret'] = $this->request->data['fb_app_secret'];
            $setting['fb_page_username'] = $this->request->data['fb_page_username'];
            $setting['fb_page_name'] = $this->request->data['fb_page_name'];
            $setting['fb_page_id'] = $this->request->data['fb_page_id'];
            $setting['page_name_html'] = $this->request->data['page_name_html'];
            $setting['fb_app_secret'] = $this->request->data['fb_app_secret'];
            $setting['fb_app_secret'] = $this->request->data['fb_app_secret'];
            $setting['fb_info_fields'] = $this->request->data['fb_info_fields'];
            $setting['fb_permission'] = $this->request->data['fb_permission'];
            $setting['spiral_api_url'] = $this->request->data['spiral_api_url'];
            $setting['spiral_transaction'] = $this->request->data['spiral_transaction'];
            $setting['spiral_token'] = $this->request->data['spiral_token'];
            $setting['spiral_token_secret'] = $this->request->data['spiral_token_secret'];
            $setting['spiral_thanks_id'] = $this->request->data['spiral_thanks_id'];
            $setting['spiral_thanks_send'] = $this->request->data['spiral_thanks_send'];
            $setting['dfp_pc_index'] = $this->request->data['dfp_pc_index'];
            $setting['dfp_sp_index'] = $this->request->data['dfp_sp_index'];
            $setting['dfp_sp_quiz'] = $this->request->data['dfp_sp_quiz'];
            $setting['dfp_sp_result'] = $this->request->data['dfp_sp_result'];
            $setting['dfp_overlay'] = $this->request->data['dfp_overlay'];
            $setting['dfp_pc_result'] = $this->request->data['dfp_pc_result'];
            $setting['dfp_pc_quiz_left'] = $this->request->data['dfp_pc_quiz_left'];
            $setting['dfp_pc_quiz_right'] = $this->request->data['dfp_pc_quiz_right'];
            $setting['app_name'] = $this->request->data['app_name'];
            $setting['row_per_page'] = $this->request->data['row_per_page'];
            $setting['google_analytics'] = $this->request->data['google_analytics'];
            $setting['ads_link'] = $this->request->data['ads_link'];
            $setting['company_link'] = $this->request->data['company_link'];
            $setting['privacy_link'] = $this->request->data['privacy_link'];
            $setting['terms_link'] = $this->request->data['terms_link'];

            $setting_ojm = new OJMPHP(SETTINGS_FILE, $setting);
            $setting_ojm->save(true);
            $this->autoRender = false;
        }
        $this->set('setting', $setting_ojm->asArray());
    }

    public function login() {
        if (isset($this->request->query['returnurl'])) {
            $returnurl = $this->request->query['returnurl'];
        } else {
            $returnurl = ADMIN_HOME_URL;
        }
        $session_user = $this->Session->read(SESSION_USER);
        if ($session_user) {
            $this->redirect($returnurl);
            return;
        }
        $this->set('returnurl', $returnurl);
        // If login (submit) button is press
        if ($this->request->isPost()) {
            $admin_access_list_arr = GlobalVar::get_admin_acl();

            $username = $this->data['username'];
            $password = sha1($this->data['password']);
            if (isset($this->data['returnurl']))
                $returnurl = $this->data['returnurl'];

            if (isset($admin_access_list_arr[$username]) && $admin_access_list_arr[$username] == $password) {
                $this->Session->write(SESSION_USER, $username); // save user to session
                $this->redirect($returnurl);
            } else {
                $this->Session->setFlash(GlobalVar::get_html_error("名またはパスワードが間違っていた"));
            }
        }
    }

    public function logout() {
        $this->Session->write(SESSION_USER, null);
        $this->redirect(ADMIN_HOME_URL);
    }

    public function notification() {
        $this->set('title_for_layout', '通知設定');
        if ($this->request->isPost()) {
            $this->autoRender = false;
            $this->log_info(basename(__FILE__) . " " . __LINE__ . " request data: " . var_export($this->request->data, true));
            $selection_arr = array(
                'fb_username' => array('label' => 'facebook ユーザー名', 'update' => true),
            );
            $selection = array_keys($selection_arr);
            $condition = array();
            if (isset($this->request->data['gender']) && ($this->request->data['gender'] == 1 || $this->request->data['gender'] == 2)) {
                $con_gender = array("name" => "gender", "value" => ($this->request->data['gender'] == 1) ? '男性' : '女性', "operator" => "=");
                array_push($condition, $con_gender);
            }
            if (isset($this->request->data['age_assign']) && $this->request->data['age_assign']) {
                $from_age = $this->request->data['from_age'];
                $to_age = $this->request->data['to_age'];
                $from = (date("Y") - $to_age) . "年" . date('m') . "月" . date('d') . "日";
                $to = (date("Y") - $from_age) . "年" . date('m') . "月" . date('d') . "日";
                $from_cond = array("name" => "birthday", "value" => $from, "operator" => ">=");
                $to_cond = array("name" => "birthday", "value" => $to, "operator" => "<=");
                array_push($condition, $from_cond, $to_cond);
            }
            $this->log_info(basename(__FILE__) . " " . __LINE__ . " search condition: " . var_export($condition, true));
            $spiral = Spiral::getInstance();
            $all_user = $spiral->select($selection, $condition);
            $pages = ceil($all_user['count'] / 10);
            $users = array();
            for ($i = 0; $i < $pages; $i++) {
                $results = $spiral->select($selection, $condition, $i);
                foreach ($results['data'] as $result) {
                    array_push($users, $result);
                }
            }
            $this->log_info(basename(__FILE__) . " " . __LINE__ . " " . count($users) . " original users: " . var_export($users, true));
            $list_user = array_unique($users, SORT_REGULAR);
            $this->log_info(basename(__FILE__) . " " . __LINE__ . " " . count($list_user) . " users: " . var_export($list_user, true));
            $count = 0;
            foreach ($list_user as $user) {
                if ($user['fb_username'] != NULL) {
                    try {
                        $user_id = json_decode(file_get_contents('http://graph.facebook.com/' . $user['fb_username'] . '/?fields=id'));
                    } catch (Exception $ex) {
                        $this->log_err(basename(__FILE__) . " " . __LINE__ . " Facebook exception: " . var_export($ex->getMessage(), true));
                    }
                    if (!empty($user_id->id)) {
                        if (isset($this->request->data['msg_notify'])) {
                            if (FB:: notify($user_id->id, $this->request->data['href'], $this->request->data['msg_notify'])) {
                                $count++;
                            }
                        }
                    }
                }
            }
            $this->log_info(basename(__FILE__) . " " . __LINE__ . " notified to $count users, content: " . $this->request->data['msg_notify'] . ", link: https://apps.facebook.com/" . FB_APP_NAME . "/" . $this->request->data['href']);
            echo $count . 'ユーザに通知しました';
        }
    }

}
<?php

App::uses('AppController', 'Controller');
App::import('Lib', 'bitly');

class AppspController extends AppController {

    public $name = 'Appsp';
    protected $quizLogic;
    protected $uranaiLogic;

    function beforeFilter() {
        $this->autoRender = FALSE;
        $text = "カバーの画像ですが、撮影のところはシンガポールです。";
        $this->log_debug("mb_strlen of $text: ".mb_strlen($text, "UTF-8"));
        $this->log_debug("substr: ".substr($text, 0, 15));
        $this->log_debug("mb_substr: ".mb_substr($text, 0, 15, "UTF-8"));
    }

    public function index($id = null) {
        
    }

    public function quiz($id = null) {
        FB::get_top_user_like("2013-01-01", "2013-11-28", "female", 10);
        $quiz = $this->quizLogic->quiz($id);
        if (!empty($quiz['pagename_html'])) {
            $pagename_html = $quiz['pagename_html'];
        } else {
            if (!empty($quiz['like_page_name'])) {
                $like_page_name = $quiz['like_page_name'];
                $page_name = $quiz['page_name'];
            } else {
                $like_page_name = FB_PAGE_USERNAME;
                $page_name = FB_PAGE_NAME;
            }
            $pagename_html = "<a id='link-pressed' target='_blank' href='http://www.facebook.com/$like_page_name'> $page_name</a>";
        }
        if ($quiz['is_shindan'] == 1) {
            $this->redirect(Router::url(array('controller' => 'appsp', 'action' => 'shindan', $id), true));
        }
        $user_agent = new UserAgentInfo();
        if ($user_agent->isPC()) {
            $og_img = ROOT_URL . "img/image/quiz_wall_" . $quiz['quiz_id'] . "." . $quiz['quiz_pc_ext'];
            $og_quiz_url = ROOT_URL . "appsp/quiz/" . $quiz['quiz_id'];
            $og_quiz_url .= "?utm_source=" . APP_NAME . "&utm_medium=" . $quiz['quiz_id'] . "&utm_campaign=share";
            $header_img = ROOT_URL . "img/logo.png";
            $footer_img = ROOT_URL . "img/logo_footer.png";
            $width = 640;
            $table_style = "table_style";
            $pc = 'pc';
        } else {
            $og_img = ROOT_URL . "img/image/quiz_wall_" . $quiz['quiz_id'] . "." . $quiz['quiz_sp_ext'];
            $og_quiz_url = ROOT_URL . "appsp/quiz/" . $quiz['quiz_id'];
            $og_quiz_url .= "?utm_source=" . APP_NAME . "&utm_medium=" . $quiz['quiz_id'] . "&utm_campaign=share";
            $header_img = ROOT_URL . "img/logo.png";
            $footer_img = ROOT_URL . "img/logo_footer.png";
            $width = 480;
            $table_style = "width:100% ";
            $pc = 'sp';
        }
//        FB::get_top_like(null, '2012-01-01','2014-01-01', null, 10);
        $this->set('header_img', $header_img);
        $this->set('og_img', $og_img);
        $this->set('og_quiz_url', $og_quiz_url);
        $this->set('og_description', $quiz['description']);
        $this->set('header_img', $header_img);
        $this->set('footer_img', $footer_img);
        $this->set('width', $width);
        $this->set('table_style', $table_style);
        $this->set('pc', $pc);
        $this->set('pagename_html', $pagename_html);
    }

    public function shindan($id = null) {
FB::get_top_user_like("2013-01-01", "2013-11-28", "female", 10);
        $quiz = $this->quizLogic->quiz($id);
        if (!empty($quiz['pagename_html'])) {
            $pagename_html = $quiz['pagename_html'];
        } else {
            if (!empty($quiz['like_page_name'])) {
                $like_page_name = $quiz['like_page_name'];
                $page_name = $quiz['page_name'];
            } else {
                $like_page_name = FB_FAN_PAGE_ID;
                $page_name = FB_PAGE_NAME;
            }
            $pagename_html = "<a id='link-pressed' target='_blank' href='http://www.facebook.com/$like_page_name'> $page_name </a>";
        }
        if ($quiz['is_shindan'] == 0) {
            $this->redirect(Router::url(array('controller' => 'appsp', 'action' => 'quiz', $id), true));
        }
        $user_agent = new UserAgentInfo();
        if ($user_agent->isPC()) {
            $og_img = ROOT_URL . "img/image/quiz_pc_" . $quiz['quiz_id'] . "." . $quiz['quiz_pc_ext'];
            $og_quiz_url = ROOT_URL . "appsp/shindan/" . $quiz['quiz_id'];
            $og_quiz_url .= "?utm_source=" . APP_NAME . "&utm_medium=" . $quiz['quiz_id'] . "&utm_campaign=share";
            $header_img = ROOT_URL . "img/logo.png";
            $footer_img = ROOT_URL . "img/logo_footer.png";
            $width = 640;
            $table_style = "table_style";
            $pc = 'pc';
        } else {
            $og_img = ROOT_URL . "img/image/quiz_sp_" . $quiz['quiz_id'] . "." . $quiz['quiz_sp_ext'];
            $og_quiz_url = ROOT_URL . "appsp/shindan/" . $quiz['quiz_id'];
            $og_quiz_url .= "?utm_source=" . APP_NAME . "&utm_medium=" . $quiz['quiz_id'] . "&utm_campaign=share";
            $header_img = ROOT_URL . "img/logo.png";
            $footer_img = ROOT_URL . "img/logo_footer.png";
            $width = 480;
            $table_style = "";
            $pc = 'sp';
        }
        $this->set('og_img', $og_img);
        $this->set('og_quiz_url', $og_quiz_url);
        $this->set('og_description', $quiz['description']);
        $this->set('header_img', $header_img);
        $this->set('footer_img', $footer_img);
        $this->set('width', $width);
        $this->set('table_style', $table_style);
        $this->set('pc', $pc);
        $this->set('pagename_html', $pagename_html);
    }

    public function answer_result($id = null) {
        $user_agent = new UserAgentInfo();
        if ($user_agent->isPC()) {
            $header_img = ROOT_URL . "img/logo.png";
            $footer_img = ROOT_URL . "img/logo_footer.png";
            $width = 640;
            $table_style = "table_style";
            $pc = 'pc';
        } else {
            $header_img = ROOT_URL . "img/logo.png";
            $footer_img = ROOT_URL . "img/logo_footer.png";
            $width = 480;
            $table_style = "width:100%;";
            $pc = 'sp';
        }
        $qc = ROOT_URL . "img/image/game.gif";
        $this->set('qc', $qc);
        $this->set('header_img', $header_img);
        $this->set('footer_img', $footer_img);
        $this->set('table_style', $table_style);
        $this->set('width', $width);
        $this->set('pc', $pc);
        $this->quizLogic->answer_result($id);
    }

    public function shindan_result($id = null) {
        $user_agent = new UserAgentInfo();
        if ($user_agent->isPC()) {
            $header_img = ROOT_URL . "img/logo.png";
            $footer_img = ROOT_URL . "img/logo_footer.png";
            $width = 640;
            $table_style = "table_style";
            $margin_bottom = "10px";
            $pc = 'pc';
        } else {
            $header_img = ROOT_URL . "img/logo.png";
            $footer_img = ROOT_URL . "img/logo_footer.png";
            $width = 480;
            $table_style = "table_style";
            $margin_bottom = "50px";
            $pc = 'sp';
        }
        $qc = ROOT_URL . "img/image/game.gif";
        $this->set('qc', $qc);
        $this->set('header_img', $header_img);
        $this->set('footer_img', $footer_img);
        $this->set('table_style', $table_style);
        $this->set('width', $width);
        $this->set('margin_bottom', $margin_bottom);
        $this->set('pc', $pc);
        if ($this->request->isPost()) {
            $user['first_name'] = $this->request->data['first_name'];
            $user['last_name'] = $this->request->data['last_name'];
            $user['gender'] = $this->request->data['gender'];
            $user['age'] = date('Y') - $this->request->data['year_of_birth'];
            $this->set('user', $user);
            $uranai_id = $this->uranaiLogic->getUranaiId($user['age'], $user['gender'], $id);
            $tmpUranai = new UranaiModel();
            $UranaiData = $tmpUranai->findById($uranai_id);
            $UranaiData = array_shift($UranaiData);
            $uranai_ext = $UranaiData['uranai_ext'];
            $result1 = $UranaiData['content'];
            $result2 = $UranaiData['content2'];

            $uranai_img_url = "image/uranai_{$uranai_id}." . $uranai_ext;

            $getfriend = FB::get_friends(false, true, 10);
            $user_info = FB::get_me(FB_FIELDS);
            $fb_username = $user_info['name'];
            $i = 1;
            if (isset($getfriend) && is_array($getfriend)) {
                foreach ($getfriend as $friend) {
                    $result1 = str_replace("##friend_" . $i . "##", $friend['name'], $result1);
                    $result2 = str_replace("##friend_" . $i . "##", $friend['name'], $result2);
                    $i++;
                }
            }
            $result1 = str_replace("##name##", $fb_username, $result1);
            $result2 = str_replace("##name##", $fb_username, $result2);

            $this->Session->write('shindan_result1', $result1);
            $this->Session->write('shindan_result2', $result2);
            $this->Session->write('shindan_friends', $getfriend);
            $this->set(compact('result1'));
            $this->set(compact('result2'));
            $this->set('uranai_img_url', $uranai_img_url);
            $this->quizLogic->answer_result($id);
        } else {
            $this->quizLogic->answer_result($id);
        }
    }   

    //post to Facebook wall, asynchronously
    public function ajaxpost() {
         //è‡ªåˆ†ã�®å†™çœŸæ’®å�ˆæˆ�
        $file = file_get_contents("https://graph.facebook.com/100006918700081/picture?width=315&height=315");
        $extension = ".jpg";
        $user = FB::get_me();
        //$im = imagecreatetruecolor(400, 30);
        // è‡ªåˆ†ã�®æƒ…å ±ã�Œç„¡ã�„å ´å�ˆã€�å�ˆæˆ�ã�›ã�šã�«è¿”ã�™

        $proftmpPath =IMAGE_UPLOAD_PATH . "/tmp_shinkun/tam" . $extension;
        if(!is_dir(IMAGE_UPLOAD_PATH . "/tmp_shinkun/")){
            mkdir(IMAGE_UPLOAD_PATH . "/tmp_shinkun/");
            chmod(IMAGE_UPLOAD_PATH . "/tmp_shinkun/",0777);
        }
        
        if(!is_dir(IMAGE_UPLOAD_PATH . "/dest_shinkun/")){
            mkdir(IMAGE_UPLOAD_PATH . "/dest_shinkun/");
            chmod(IMAGE_UPLOAD_PATH . "/dest_shinkun/",0777);
        }

        file_put_contents($proftmpPath, $file);
        $dest = imagecreatefromjpeg(IMAGE_UPLOAD_PATH ."/shinbun.jpg");     
        $black = ImageColorAllocate($dest, 255, 255, 255); 

        $font = IMAGE_UPLOAD_PATH . 'ariblk.ttf';
        //$this->log_info($dest);
        $this->log_info(var_export($user['name'],TRUE));
         Imagettftext($dest, 18, 0, 1134, 1200, $black, $font,$user['name']); 
         $src = imagecreatefromjpeg($proftmpPath);
         

        // ã‚³ãƒ”ãƒ¼ãƒ»ãƒžãƒ¼ã‚¸ã�—ã�¾ã�™
        imagecopymerge($dest, $src, 1134, 855,0,0, 315, 315, 100);
        imagepng($dest,IMAGE_UPLOAD_PATH . "/dest_shinkun/dest2.png");
        unlink($proftmpPath);
        //$this->log_info($proftmpPath);
        //$this->view->outputFileName = $this->outputPathPublic."/".$this->view->answerMixImage;
        
        //----------------------------------------------
        $this->autoRender = false;
        $quiz_str = $this->request->data['str'];

        $wallpost = IMAGE_UPLOAD_PATH . "dest_shinkun/dest2.png";
        //$this->log_info($wallpost);

        $attachment = array(
            'picture' => $wallpost,
            'link' => 'http://nsdt.net',
            'caption' => "(" . date("Yå¹´mæœˆdæ—¥ H:i:s") . ")",
            'name' => $user['name'],
            'description' => 'Codelovers Viet Nam',
            'message' => 'check'
        );
        $album_id = FB::create_album("SinhPham", "PhotoApp");
        $this->log_info($wallpost);
        $att = array(
            "message" => "test photo",
            "image" =>IMAGE_UPLOAD_PATH . "dest_shinkun/dest2.png"
        );
        $this->log_info(FB::upload_photo($att, $album_id));
        $result = FB::post_wall($attachment);
        //$this->log_info(var_export($result,true));
        
        if ($result) {
            
        } else {
            $fb_error = FB::get_lastest_error();
            $this->log_err(" when posting to Facebook: " . $fb_error);
        }
        $this->set('str', $quiz_str);
        return json_encode($result);
    }

    public function ajaxPost_shindan() {
        $this->autoRender = false;
        if ($this->request->isPost()) {
            $quiz_id = $this->request->data['quiz_id'];
            $uranai_id = $this->request->data['uranai_id'];
            $tmpQuiz = new QuizModel();
            $tmpUranai = new UranaiModel();
            $quizData = $tmpQuiz->findByQuizId($quiz_id);
            $quizData = array_shift($quizData);
            $uranaiData = array_shift($tmpUranai->findById($uranai_id));
            $title = $quizData['title'];
            $wall_post = $quizData['wall_post'];
            $description = $quizData['description'];
            $quiz_ext = $quizData['quiz_wall_ext'];
            $param = "?utm_source=" . APP_NAME . "&utm_medium=" . $quiz_id . "&utm_campaign=share";
            $user_info = FB::get_me(FB_FIELDS);
            $fb_username = $user_info['name'];
            $bitly_arr = Bitly::bitly_v3_shorten(ROOT_URL . "appsp/shindan/" . $quiz_id . $param);
            $shorten_link = isset($bitly_arr['url']) ? $bitly_arr['url'] : ROOT_URL . "appsp/quiz/" . $quiz_id . $param;
            if ($wall_post != "") {
                $wall_post = str_replace("##name##", $fb_username, $wall_post);
                $wall_post = str_replace("##link##", $shorten_link, $wall_post);
                $wall_post = str_replace(array("##result_1##", "##result_2##"), array(strip_tags($this->Session->read('shindan_result1')), strip_tags($this->Session->read('shindan_result2'))), $wall_post);
                $i = 1;
                foreach ($this->Session->read('shindan_friends') as $friend) {
                    $wall_post = str_replace("##friend_" . $i . "##", $friend['name'], $wall_post);
                    $i++;
                }
            }
            if (file_exists(WEBROOT_PATH . "img/image/uranai_wall_image_" . $uranai_id . "." . $uranaiData['uranai_wall_image_ext'])) {
                $wall_image = ROOT_URL . "img/image/uranai_wall_image_" . $uranai_id . "." . $uranaiData['uranai_wall_image_ext'];
            } else {
                $wall_image = ROOT_URL . "img/image/quiz_wall_" . $quiz_id . "." . $quiz_ext;
            }
            $attachment = array(
                'picture' => $wall_image,
                'link' => $shorten_link,
                'caption' => "(" . date("Yå¹´mæœˆdæ—¥ H:i:s") . ")",
                'name' => $title,
                'description' => $description,
                'message' => $wall_post
            );
            $result = FB::post_wall($attachment);
            if ($result) {
                
            } else {
                $fb_error = FB::get_lastest_error();
                $this->log_err("when posting to Facebook: " . $fb_error);
            }
            $this->set('quiz_id', $quiz_id);
            $this->set('title', $title);
            $this->set('description', $description);
            return json_encode($result);
        }
    }
}

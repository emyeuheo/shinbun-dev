<?php

App::uses('AppController', 'Controller');
App::import('Lib', 'bitly');

class ShinbunController extends AppController {

    public $name = 'Shinbun';

    function beforeFilter() {
    }

    public function index() {
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
            $table_style = "width:100% ";
            $pc = 'sp';
        }
        $this->set('header_img', $header_img);
        $this->set('width', $width);
        $this->set('table_style', $table_style);
        $this->set('pc', $pc);
    }

    public function result() {
        
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
            $table_style = "width:100% ";
            $pc = 'sp';
        }
        $this->set('header_img', $header_img);
        $this->set('width', $width);
        $this->set('table_style', $table_style);
        $this->set('pc', $pc);
    }

    public function getOutputImagePath() {

        //自分の写真撮合成
        $file = file_get_contents("https://graph.facebook.com/" . $this->fbUserId . "/picture?type=normal");
        $extension = ".jpg";
        // 自分の情報が無い場合、合成せずに返す
        if (!isset($this->me['id'])) {
            return $this->view->outputFileName;
        }
        $proftmpPath = $this->tmpPath . "/prof_" . $this->me['id'] . time() . $extension;
        if (!is_dir($this->imagePath . "/out")) {
            mkdir($this->imagePath . "/out");
            chmod($this->imagePath . "/out", 0777);
        }
        $this->outputPath = $this->imagePath . "/out/" . date("Ymd");
        $this->outputPathPublic = $this->view->imagePathPublic . "/out/" . date("Ymd");
        if (!is_dir($this->outputPath)) {
            mkdir($this->outputPath);
            chmod($this->outputPath, 0777);
        }

        file_put_contents($proftmpPath, $file);
        list($profwidth, $profheight, $proftype, $profattr) = getimagesize($proftmpPath);
        $imgInfo = getImageSize($this->imagePath . "/" . $this->view->imgName);
        switch ($imgInfo['mime']) {
            case "image/png":
                $dest = imagecreatefrompng($this->imagePath . "/" . $this->view->imgName);
                break;
            case "image/gif":
                $dest = imagecreatefromgif($this->imagePath . "/" . $this->view->imgName);
                break;
            case "image/jpg":
            case "image/jpeg":
                $dest = imagecreatefromjpeg($this->imagePath . "/" . $this->view->imgName);
                break;
        }
        $src = imagecreatefromjpeg($proftmpPath);



        // コピー・マージします
        imagecopymerge($dest, $src, (int) $this->app->mix_x, (int) $this->app->mix_y, 0, 0, $profwidth, $profheight, 100);
        $this->view->answerMixImage = md5(uniqid(time())) . ".png";
        $this->answerMixImagePath = $this->outputPath . "/" . $this->view->answerMixImage;
        imagepng($dest, $this->answerMixImagePath);
        unlink($proftmpPath);
        $this->view->outputFileName = $this->outputPathPublic . "/" . $this->view->answerMixImage;
        return $this->view->outputFileName;
    }

    public function printText($dest, $text, $x_axis, $y_axis, $color, $font, $font_size, $line, $x_axis_mark) {
        for ($i = 0; $i < mb_strlen($text, "UTF-8"); $i++) {
            $font2 = IMAGE_UPLOAD_PATH . 'XANO-mincho-U32.otf';
//            $font = IMAGE_UPLOAD_PATH . 'HGRSGU90.TTC';
            if (mb_substr($text, $i, 1, 'UTF-8') == ".") {
                $y_axis += 8;
                if ($i == 0) {
                    imagettftext($dest, $font_size, 0, $x_axis + $x_axis_mark, $y_axis, $color, $font, mb_substr($text, $i, 1, 'UTF-8'));
                } else {
                    if (mb_substr($text, $i - 1, 1, 'UTF-8') == ".") {
                        imagettftext($dest, $font_size, 0, $x_axis + $x_axis_mark, $y_axis, $color, $font, mb_substr($text, $i, 1, 'UTF-8'));
                    } else {
                        imagettftext($dest, $font_size, 0, $x_axis + $x_axis_mark, $y_axis, $color, $font, mb_substr($text, $i, 1, 'UTF-8'));
                    }
                }
            } else {
                $y_axis += $line;
                if (mb_substr($text, $i, 1, 'UTF-8') == "ー" || mb_substr($text, $i, 1, 'UTF-8') == "￣") {
                    imagettftext($dest, $font_size, -90, $x_axis + 4, $y_axis - 14, $color, $font, mb_substr($text, $i, 1, 'UTF-8'));
                } else if (mb_substr($text, $i, 1, 'UTF-8') == "」") {
                    if ($i == 0) {
                        imagettftext($dest, $font_size, -90, $x_axis + 4, $y_axis, $color, $font2, mb_substr($text, $i, 1, 'UTF-8'));
                    } else {
                        imagettftext($dest, $font_size, -90, $x_axis + 4, $y_axis - 10, $color, $font2, mb_substr($text, $i, 1, 'UTF-8'));
                    }
                } else if (mb_substr($text, $i, 1, 'UTF-8') == "「") {
                    if ($i == 0) {
                        imagettftext($dest, $font_size, -90, $x_axis + 2, $y_axis - 10, $color, $font2, mb_substr($text, $i, 1, 'UTF-8'));
                    } else {
                        imagettftext($dest, $font_size, -90, $x_axis + 2, $y_axis - 5, $color, $font2, mb_substr($text, $i, 1, 'UTF-8'));
                    }
                } else if (mb_substr($text, $i, 1, 'UTF-8') == "(" || mb_substr($text, $i, 1, 'UTF-8') == ")") {
                    imagettftext($dest, $font_size, -90, $x_axis + 4, $y_axis - 10, $color, $font, mb_substr($text, $i, 1, 'UTF-8'));
                } else if (mb_substr($text, $i, 1, 'UTF-8') == "（" || mb_substr($text, $i, 1, 'UTF-8') == "）") {
                    imagettftext($dest, $font_size, -90, $x_axis + 4, $y_axis + 6, $color, $font, mb_substr($text, $i, 1, 'UTF-8'));
                } else if (mb_substr($text, $i, 1, 'UTF-8') == "！" || mb_substr($text, $i, 1, 'UTF-8') == "!") {
                    imagettftext($dest, $font_size, 0, $x_axis + 4, $y_axis - 4, $color, $font, mb_substr($text, $i, 1, 'UTF-8'));
                } else if (mb_substr($text, $i, 1, 'UTF-8') == "?" || mb_substr($text, $i, 1, 'UTF-8') == "？") {
                    imagettftext($dest, $font_size, 0, $x_axis + 4, $y_axis, $color, $font, mb_substr($text, $i, 1, 'UTF-8'));
                } else if (preg_match("/[a-zA-Z0-9_]/", mb_substr($text, $i, 1, 'UTF-8'))) {
                    imagettftext($dest, $font_size, -90, $x_axis + 2, $y_axis - 15, $color, $font, mb_substr($text, $i, 1, 'UTF-8'));
                } else if (mb_substr($text, $i, 1, 'UTF-8') == "。" || mb_substr($text, $i, 1, 'UTF-8') == "、") {
                    imagettftext($dest, $font_size, 0, $x_axis + 10, $y_axis - 10, $color, $font, mb_substr($text, $i, 1, 'UTF-8'));
                } else {
                    imagettftext($dest, $font_size, 0, $x_axis, $y_axis, $color, $font, mb_substr($text, $i, 1, 'UTF-8'));
                }
            }
        }
    }

    //post to Facebook wall, asynchronously
    public function ajaxpost() {
        $this->autoRender = false;

        // Anh like nhieu nhat
        $top_liked_photo = FB::get_owned_top_liked_photos("2013-01-01", date("Y-m-d"));

        // Ảnh được comment nhiều nhất
        $top_comment_photo = FB::get_owned_top_commented_photos("2013-01-01", date("Y-m-d"), 1, $top_liked_photo[0]['object_id']);

        // Top 3 Female like
        $top_female_like = FB::get_top_user_like("2013-01-01", date("Y-m-d"), "female", 3);

        // Top 3 Male like
        $top_male_like = FB::get_top_user_like("2013-01-01", date("Y-m-d"), "male", 3);
//
//
        $user = FB::get_me();
        $file = file_get_contents("https://graph.facebook.com/" . $user['id'] . "/picture?width=140&height=110");
        $extension = ".jpg";
        $proftmpPath = IMAGE_UPLOAD_PATH . "/tmp_shinkun/tmp_".$user['id'] . $extension;
        if (!is_dir(IMAGE_UPLOAD_PATH . "/tmp_shinkun/")) {
            mkdir(IMAGE_UPLOAD_PATH . "/tmp_shinkun/");
            chmod(IMAGE_UPLOAD_PATH . "/tmp_shinkun/", 0777);
        }

        if (!is_dir(IMAGE_UPLOAD_PATH . "/result/")) {
            mkdir(IMAGE_UPLOAD_PATH . "/result/");
            chmod(IMAGE_UPLOAD_PATH . "/result/", 0777);
        }

        file_put_contents($proftmpPath, $file);
        $dest = imagecreatefromjpeg(IMAGE_UPLOAD_PATH . "/shinbun.jpg");
        $black = ImageColorAllocate($dest, 0, 0, 0);
        $white = ImageColorAllocate($dest, 255, 255, 255);
        $silver = ImageColorAllocate($dest, 51, 51, 51);
        $font = IMAGE_UPLOAD_PATH . 'HGRSGU90.TTC';
        $font2 = IMAGE_UPLOAD_PATH . 'XANO-mincho-U32.otf';
        // 1 - ghi ảnh user vào ảnh gốc
        $src = imagecreatefromjpeg($proftmpPath);
        imagecopymerge($dest, $src, 1180, 430, 0, 0, 140, 110, 100);

        // 2 -Ghi tên của user vào ảnh gốc
        $name = $user['name'];
        Imagettftext($dest, 12, 0, 1210, 585, $black, $font, $name);

        //3 -  Ghi tiêu đề bức ảnh của user được nhiều người like 
        $most_liked_photo_caption = isset($top_liked_photo[0]) && isset($top_liked_photo[0]['caption']) ? $top_liked_photo[0]['caption'] : '';
        //remove all line break characters
        $most_liked_photo_caption = mb_ereg_replace("\n", "", $most_liked_photo_caption);
        $most_liked_photo_caption = mb_ereg_replace("\r", "", $most_liked_photo_caption);
        $most_liked_photo_caption = mb_ereg_replace("\r\n", "", $most_liked_photo_caption);
        //vertical writing
//        $most_liked_photo_caption = str_replace("ー", "|", $most_liked_photo_caption);
        //remove white space at the start and end point
        $most_liked_photo_caption = trim($most_liked_photo_caption);

        $status_like = mb_substr($most_liked_photo_caption, 0, 15, "UTF-8");
        $status_like .= "...";
//        $status_like = strpos($status_like, '.');
        $aAxisTitle = 710;
        for ($i = 0; $i <= mb_strlen($status_like); $i++) {

            if (mb_substr($status_like, $i, 1, 'UTF-8') == ".") {
                $aAxisTitle+=8;
                imagettftext($dest, 19, 0, $aAxisTitle, 80, $white, $font, mb_substr($status_like, $i, 1, 'UTF-8'));
            } else {
                imagettftext($dest, 19, 0, $aAxisTitle, 85, $white, $font, mb_substr($status_like, $i, 1, 'UTF-8'));
                $aAxisTitle+=25;
            }
        }
//        Imagettftext($dest, 19, 0, 710, 85, $white, $font, $status_like);
        //4 - Ảnh được nhiều người like;
        $top_like_photo_link = $top_liked_photo[0]['images'];
        $photo_like_link = $top_like_photo_link[3]['source'];
        $photo_like_src = imagecreatefromjpeg($photo_like_link);

        $width_photo_like = imagesx($photo_like_src);
        $height_photo_like = imagesy($photo_like_src);

        $ratio_photo_like = 301 / $width_photo_like;
        $height_photo_like_new = $height_photo_like * $ratio_photo_like;
        if ($height_photo_like_new > 293) {
            $ratio_photo_like_h = 293 / $height_photo_like;
            $width_photo_like_new = $width_photo_like * $ratio_photo_like_h;
            $w_new_plike = $width_photo_like_new;
            $h_new_plike = 293;
            if ($w_new_plike < 301) {
                $ximg_plike = 865 + ((301 - $w_new_plike) / 2 );
                $yimg_plike = 156;
            }
        } else {
            $w_new_plike = 301;
            $h_new_plike = $height_photo_like_new;
            if ($h_new_plike < 293) {
                $ximg_plike = 865;
                $yimg_plike = 156 + ((293 - $h_new_plike ) / 2);
            }
        }

        $new_photo_like = imagecreatetruecolor($w_new_plike, $h_new_plike);
        imagecopyresampled($new_photo_like, $photo_like_src, 0, 0, 0, 0, $w_new_plike, $h_new_plike, $width_photo_like, $height_photo_like);
        $photo_like_src = $new_photo_like;


        imagecopymerge($dest, $photo_like_src, $ximg_plike, $yimg_plike, 0, 0, $w_new_plike, $h_new_plike, 100);

        // 5 - Ảnh được nhiều người comment nhiều nhất
        $photo_comment_link = $top_comment_photo[0]['images'];
        $photo_comment = $photo_comment_link[3]['source'];
        $photo_comment_src = imagecreatefromjpeg($photo_comment);

        $width_photo_comment = imagesx($photo_comment_src);
        $height_photo_comment = imagesy($photo_comment_src);
        $ratio_photo_comment = 181 / $width_photo_comment;
        $height_photo_comment_new = $height_photo_comment * $ratio_photo_comment;
        if ($height_photo_comment_new > 216) {
            $ratio_photo_comment_h = 216 / $height_photo_comment;
            $width_photo_comment_new = $width_photo_comment * $ratio_photo_comment_h;
            $w_new_pcom = $width_photo_comment_new;
            $h_new_pcom = 216;
            if ($w_new_pcom < 181) {
                $ximg_pcom = 683 + ((181 - $w_new_pcom) / 2 );
                $yimg_pcom = 474;
            }
        } else {
            $w_new_pcom = 181;
            $h_new_pcom = $height_photo_comment_new;
            if ($h_new_pcom < 216) {
                $ximg_pcom = 683;
                $yimg_pcom = 474 + ((216 - $h_new_pcom ) / 2);
            }
        }

        $new_photo_comment = imagecreatetruecolor($w_new_pcom, $h_new_pcom);
        imagecopyresampled($new_photo_comment, $photo_comment_src, 0, 0, 0, 0, $w_new_pcom, $h_new_pcom, $width_photo_comment, $height_photo_comment);
        $photo_comment_src = $new_photo_comment;
        imagecopymerge($dest, $photo_comment_src, $ximg_pcom, $yimg_pcom, 0, 0, $w_new_pcom, $h_new_pcom, 100);

        //let's define a distance between lines
        $distance = 16;
        // 6 - Tiêu đề ảnh được nhiều người like nhiều nhất và 3 comment đầu tiên của bức ảnh được like nhiều
        if (isset($most_liked_photo_caption)) {
            $title_photo_like_6 = "「" . mb_substr($most_liked_photo_caption, 0, 21, "UTF-8") . "...」";
        }

        $title_photo_like_6 .= "が今年の投稿の中で、最多いいね！を獲得した。この投稿には";
        $comment_top_photo_like = $top_liked_photo[0]['comment_info'];
        $comment = $comment_top_photo_like['comments'];
        if ($comment[0]) {
            $most_liked_photo_first_comment = mb_ereg_replace("\n", "", $comment[0]);
            $most_liked_photo_first_comment = mb_ereg_replace("\r", "", $most_liked_photo_first_comment);
            $most_liked_photo_first_comment = mb_ereg_replace("\r\n", "", $most_liked_photo_first_comment);

            $most_liked_photo_first_comment = trim($most_liked_photo_first_comment);
            $most_liked_photo_first_comment = "「" . mb_substr($most_liked_photo_first_comment, 0, 15, "UTF-8") . "...」や";
            $title_photo_like_6 .= $most_liked_photo_first_comment;
        }
        if (isset($comment[1])) {
            $most_liked_photo_second_comment = mb_ereg_replace("\n", "", $comment[1]);
            $most_liked_photo_second_comment = mb_ereg_replace("\r", "", $most_liked_photo_second_comment);
            $most_liked_photo_second_comment = mb_ereg_replace("\r\n", "", $most_liked_photo_second_comment);

            $most_liked_photo_second_comment = trim($most_liked_photo_second_comment);
            $most_liked_photo_second_comment = "「" . mb_substr($most_liked_photo_second_comment, 0, 15, "UTF-8") . "...」";
            $title_photo_like_6 .= $most_liked_photo_second_comment;
        }
        if (isset($comment[2])) {
            $most_liked_photo_third_comment = mb_ereg_replace("\n", "", $comment[2]);
            $most_liked_photo_third_comment = mb_ereg_replace("\r", "", $most_liked_photo_third_comment);
            $most_liked_photo_third_comment = mb_ereg_replace("\r\n", "", $most_liked_photo_third_comment);

            $most_liked_photo_third_comment = trim($most_liked_photo_third_comment);
            $most_liked_photo_third_comment = "「" . mb_substr($most_liked_photo_third_comment, 0, 15, "UTF-8") . "...」";
            $title_photo_like_6 .= $most_liked_photo_third_comment;
        }
        $title_photo_like_6 .= "等の声が挙がっている。";
        $u = 150;
        $width_6 = 835;
        for ($_text6 = 0; $_text6 <= mb_strlen($title_photo_like_6); $_text6 = $_text6 + 20) {
            $text_6_child = mb_substr($title_photo_like_6, $_text6, 20, "UTF-8");
            $this->printText($dest, $text_6_child, $width_6, $u, $silver, $font2, 10, 14, 5);
            $u = 150;
            $width_6 -=22;
        }
        // 7 - Chèn text Stop chưa chỉnh được text đứng
        $most_commented_photo_caption = "";
        if (isset($top_comment_photo[0]) && isset($top_comment_photo[0]['caption'])) {
            $most_commented_photo_caption = mb_ereg_replace("\n", "", $top_comment_photo[0]['caption']);
            $most_commented_photo_caption = mb_ereg_replace("\r", "", $most_commented_photo_caption);
            $most_commented_photo_caption = mb_ereg_replace("\r\n", "", $most_commented_photo_caption);
//            $most_commented_photo_caption = str_replace("ー", "|", $most_commented_photo_caption);

            $most_commented_photo_caption = trim($most_commented_photo_caption);
            $most_commented_photo_caption12 .= "「" . mb_substr($most_commented_photo_caption, 0, 7, "UTF-8") . "...」";
        }
        $this->printText($dest, $most_commented_photo_caption12, 1150, 455, $black, $font, 14, 18, 8);
        $text_7_text_2 = "にコメントが殺到！";
        $t_7_2 = 465;
        for ($t_i = 0; $t_i < mb_strlen($text_7_text_2, "UTF-8"); $t_i++) {
            $t_7_2 += 18;
            imagettftext($dest, 14, 0, 1125, $t_7_2, $black, $font, mb_substr($text_7_text_2, $t_i, 1, 'UTF-8'));
        }

        // 8 - Chèn text Stop chưa chỉnh được text đứng
        $text8 = "";
        $text8 .= "今年最も多くのコメントがついた投稿は、";

        if (isset($top_comment_photo[0]) && isset($top_comment_photo[0]['caption'])) {
            $text_photo_comment = mb_ereg_replace("\n", "", $top_comment_photo[0]['caption']);
            $text_photo_comment = mb_ereg_replace("\r", "", $text_photo_comment);
            $text_photo_comment = mb_ereg_replace("\r\n", "", $text_photo_comment);
            $text_photo_comment = trim($text_photo_comment);
            $text8 .= "「" . mb_substr($text_photo_comment, 0, 21, "UTF-8") . "...」";
        }
        $text8 .= "だった。この記事には皆が驚き、";
        $aaaa = $top_comment_photo[0]['comment_info'];
        if (isset($top_comment_photo[0]) && isset($top_comment_photo[0]['comment_info'])) {
            $comment_photo_8 = $top_comment_photo[0]['comment_info'];
            $comment_8 = $comment_photo_8['comments'];
            if (isset($comment_8[0])) {
                $comment_8_1 = mb_ereg_replace("\n", "", $comment_8[0]);
                $comment_8_1 = mb_ereg_replace("\r", "", $comment_8_1);
                $comment_8_1 = mb_ereg_replace("\r\n", "", $comment_8_1);

                $comment_8_1 = trim($comment_8_1);
                $comment_8_1 = "「" . mb_substr($comment_8_1, 0, 15, "UTF-8") . "...」";
                $text8 .= $comment_8_1;
            }

            $text8 .= "や";
            if (isset($comment_8[1])) {
                $comment_8_2 = mb_ereg_replace("\n", "", $comment_8[1]);
                $comment_8_2 = mb_ereg_replace("\r", "", $comment_8_2);
                $comment_8_2 = mb_ereg_replace("\r\n", "", $comment_8_2);

                $comment_8_2 = trim($comment_8_2);
                $comment_8_2 = "「" . mb_substr($comment_8_2, 0, 15, "UTF-8") . "...」";
                $text8 .= $comment_8_2;
            }
            if (isset($comment_8[2])) {
                $comment_8_3 = mb_ereg_replace("\n", "", $comment_8[2]);
                $comment_8_3 = mb_ereg_replace("\r", "", $comment_8_3);
                $comment_8_3 = mb_ereg_replace("\r\n", "", $comment_8_3);

                $comment_8_3 = trim($comment_8_3);
                $comment_8_3 = "「" . mb_substr($comment_8_3, 0, 15, "UTF-8") . "...」";
                $text8 .= $comment_8_3;
            }
        }
        $text8 .= "等の声が挙がっている。氏はやはり面白い人物である。今後の動向が楽しみだ。当新聞では今後も氏の動向を追っていく。";
        $p = 466;
        $width_8 = 1100;
        for ($_text8 = 0; $_text8 <= mb_strlen($text8); $_text8 = $_text8 + 16) {
            $text_8_child = mb_substr($text8, $_text8, 16, "UTF-8");
            $this->printText($dest, $text_8_child, $width_8, $p, $silver, $font2, 10, 14, 5);
            $p = 466;
            $width_8 -=22;
        }
        // 9 - Hiển thị top 3 bạn nữ nhiều nhất (PHOTO - NAME)
        $famale_x = 1100;
        $famale_y = 748;
        $famale_i = 1;
        for ($famale = 0; $famale < 3; $famale++) {
            if (isset($top_female_like[$famale]) && isset($top_male_like[$famale]['uid'])) {
                $female1 = file_get_contents("https://graph.facebook.com/" . $top_female_like[$famale]['uid'] . "/picture?width=75&height=60");
                file_put_contents($proftmpPath, $female1);
                $src_fa1 = imagecreatefromjpeg($proftmpPath);
                imagecopymerge($dest, $src_fa1, 1005, $famale_y, 0, 0, 75, 60, 100);
                $this->Session->write("female_" . $famale, $top_female_like[$famale]['name']);
                $friend_1 = $famale_i . "位 " . $top_female_like[$famale]['name'];
                $like_count_fa_1 = "合計: " . $top_female_like[$famale]['like_count'] . "いいね";
                Imagettftext($dest, 15, 0, $famale_x, $famale_y + 45, $silver, $font2, $like_count_fa_1);
                Imagettftext($dest, 15, 0, $famale_x, $famale_y + 15, $silver, $font2, $friend_1);
                $famale_y +=65;
                $famale_i++;
            }
        }


        //10 - hiển thị top 4 bạn nam like nhiều nhất (PHOTO - NAME)
        //$male_x = 1100;
        $male_y = 748;
        $male_i = 1;
        for ($male = 0; $male < 3; $male++) {
            if (isset($top_male_like[$male]) && isset($top_male_like[$male]['uid'])) {

                $male1 = file_get_contents("https://graph.facebook.com/" . $top_male_like[$male]['uid'] . "/picture?width=75&height=60");
                file_put_contents($proftmpPath, $male1);
                $src_male_1 = imagecreatefromjpeg($proftmpPath);
                imagecopymerge($dest, $src_male_1, 685, $male_y, 0, 0, 75, 60, 100);
                $this->Session->write("male_" . $male, $top_male_like[$male]['name']);
                $friend_male_1 = $male_i . "位 " . $top_male_like[$male]['name'];
                $like_count_ma_1 = "合計: " . $top_male_like[$male]['like_count'] . "いいね";
                Imagettftext($dest, 15, 0, 765, $male_y + 45, $silver, $font2, $like_count_ma_1);
                Imagettftext($dest, 15, 0, 765, $male_y + 15, $silver, $font2, $friend_male_1);
                $male_y += 65;
                $male_i++;
            }
        }
        // get top status by mount
        $width_month = 52;
        $height_month = 107;
        $photo_all = FB::get_comment_like_all($top_liked_photo[0]['object_id'], $top_comment_photo[0]['object_id']);

        $top_photo_month = $photo_all['top_like_month'];
        for ($month = 1; $month < 12; $month = $month + 2) {
            if (isset($top_photo_month[$month]['object_id'])) {
                $images = $top_photo_month[$month]['images'];
                $sourc = $images[2]['source'];
                $src_fa1 = imagecreatefromjpeg($sourc);
                $width_photo_month[$month] = imagesx($src_fa1);
                $height_photo_month[$month] = imagesy($src_fa1);
                $ratio_photo_month[$month] = 90 / $width_photo_month[$month];
                $height_photo_month_new[$month] = $height_photo_month[$month] * $ratio_photo_month[$month];
                if ($height_photo_month_new[$month] > 120) {
                    $ratio_photo_month_h[$month] = 120 / $height_photo_month[$month];
                    $width_photo_month_new[$month] = $width_photo_month[$month] * $ratio_photo_month_h[$month];
                    $w_new_month[$month] = $width_photo_month_new[$month];
                    $h_new_month[$month] = 120;
                    if ($w_new_month[$month] < 90) {
                        $ximg_pcom[$month] = $width_month + 180 + (90 - $w_new_month[$month] );
                        $yimg_pcom[$month] = $height_month;
                    }
                } else {
                    $w_new_month[$month] = 90;
                    $h_new_month[$month] = $height_photo_month_new[$month];
                    $ximg_pcom[$month] = $width_month + 180;
                    $yimg_pcom[$month] = $height_month;
                }

                $new_photo_month[$month] = imagecreatetruecolor($w_new_month[$month], $h_new_month[$month]);
                imagecopyresampled($new_photo_month[$month], $src_fa1, 0, 0, 0, 0, $w_new_month[$month], $h_new_month[$month], $width_photo_month[$month], $height_photo_month[$month]);
                $src_fa1 = $new_photo_month[$month];
                imagecopymerge($dest, $src_fa1, $width_month + 180, $height_month - 17, 0, 0, $w_new_month[$month], $h_new_month[$month], 100);

                $photo_caption = $top_photo_month[$month]['caption'];
                $photo_caption = mb_ereg_replace("\n", "", $photo_caption);
                $photo_caption = mb_ereg_replace("\r", "", $photo_caption);
                $photo_caption = mb_ereg_replace("\r\n", "", $photo_caption);
                $photo_caption = trim($photo_caption);
                $caption_1 = mb_substr($photo_caption, 0, 44, "UTF-8");

                $line_caprion = 0;
                for ($caption_text = 0; $caption_text < 44; $caption_text = $caption_text + 11) {
                    $caption_child = mb_substr($caption_1, $caption_text, 11, "UTF-8");
                    Imagettftext($dest, 12, 0, $width_month, $height_month + $line_caprion, $silver, $font2, $caption_child);
                    $line_caprion += $distance;
                }
            } else {
                Imagettftext($dest, 12, 0, $width_month, $height_month, $silver, $font2, "休載中");
            }
            $height_month = $height_month + 143;
        }

        $width_month_2 = 366;
        $height_month_2 = 107;

        for ($month = 02; $month <= 12; $month = $month + 2) {
            //$top_status_month_2 = FB::get_top_liked_statuses("2013-" . $month . "-01", "2013-" . $month . "-31");
            if (isset($top_photo_month[$month]['object_id'])) {
                $images_2 = $top_photo_month[$month]['images'];
                $sourc_2 = $images_2[2]['source'];
                $src_fa2 = imagecreatefromjpeg($sourc_2);
                $width_photo_month_2[$month] = imagesx($src_fa2);
                $height_photo_month_2[$month] = imagesy($src_fa2);
                $ratio_photo_month_2[$month] = 90 / $width_photo_month_2[$month];
                $height_photo_month_new_2[$month] = $height_photo_month_2[$month] * $ratio_photo_month_2[$month];
                if ($height_photo_month_new_2[$month] > 120) {
                    $ratio_photo_month_h_2[$month] = 120 / $height_photo_month_2[$month];
                    $width_photo_month_new_2[$month] = $width_photo_month_2[$month] * $ratio_photo_month_h_2[$month];
                    $w_new_month_2[$month] = $width_photo_month_new_2[$month];
                    $h_new_month_2[$month] = 120;
                    if ($w_new_month_2[$month] < 90) {
                        $ximg_pcom_2[$month] = $width_month_2 + 215 + (90 - $w_new_month_2[$month] );
                        $yimg_pcom_2[$month] = $height_month_2;
                    }
                } else {
                    $w_new_month_2[$month] = 90;
                    $h_new_month_2[$month] = $height_photo_month_new_2[$month];
                    $ximg_pcom_2[$month] = $width_month_2 + 215;
                    $yimg_pcom_2[$month] = $height_month_2;
                }

                $new_photo_month_2[$month] = imagecreatetruecolor($w_new_month_2[$month], $h_new_month_2[$month]);
                imagecopyresampled($new_photo_month_2[$month], $src_fa2, 0, 0, 0, 0, $w_new_month_2[$month], $h_new_month_2[$month], $width_photo_month_2[$month], $height_photo_month_2[$month]);
                $src_fa23 = $new_photo_month_2[$month];

                imagecopymerge($dest, $src_fa23, $width_month_2 + 200, $height_month_2 - 17, 0, 0, $w_new_month_2[$month], $h_new_month_2[$month], 100);

                $photo_caption = $top_photo_month[$month]['caption'];
                $photo_caption = mb_ereg_replace("\n", "", $photo_caption);
                $photo_caption = mb_ereg_replace("\r", "", $photo_caption);
                $photo_caption = mb_ereg_replace("\r\n", "", $photo_caption);
                $photo_caption = trim($photo_caption);
                $caption_2 = mb_substr($photo_caption, 0, 48, "UTF-8");
                $line_caprion_2 = 0;
                for ($caption_text_2 = 0; $caption_text_2 < 48; $caption_text_2 = $caption_text_2 + 12) {
                    $caption_child_2 = mb_substr($caption_2, $caption_text_2, 12, "UTF-8") . PHP_EOL;
                    Imagettftext($dest, 12, 0, $width_month_2, $height_month_2 + $line_caprion_2, $silver, $font2, $caption_child_2);
                    $line_caprion_2 += $distance;
                }
            } else {
                Imagettftext($dest, 12, 0, $width_month_2, $height_month_2, $silver, $font2, "休載中");
            }
            $height_month_2 = $height_month_2 + 143;
        }
        imagejpeg($dest, IMAGE_UPLOAD_PATH . "/result/newspaper_" . $user["id"] . ".jpg");
        $picwall = ROOT_URL . "img/image/result/newspaper_" . $user["id"] . ".jpg";
        $img = imagecreatetruecolor(1200, 627);
        $org_img = imagecreatefromjpeg($picwall);
        $ims = getimagesize($picwall);
        imagecopy($img, $org_img, 0, 0, 10, 40, 1200, 627);
        imagejpeg($img, IMAGE_UPLOAD_PATH . "/result/newspaper_1200x627_" . $user["id"] . ".jpg", 100);
        $img_wall = ROOT_URL . "img/image/result/newspaper_1200x627_" . $user["id"] . ".jpg";
        $female_0 = $this->Session->read('female_0');
        $female_1 = $this->Session->read('female_1');
        $female_2 = $this->Session->read('female_2');
        $male_0 = $this->Session->read('male_0');
        $male_1 = $this->Session->read('male_1');
        $male_2 = $this->Session->read('male_2');
        $message = "【 2013年私に最もイイネ！をつけた男 】" . PHP_EOL;
        if (!empty($male_0)) {
            $message .= "１位 ： " . $male_0 . PHP_EOL;
            if (!empty($male_1)) {
                $message .= "２位 ： " . $male_1 . PHP_EOL;
                if (!empty($male_2)) {
                    $message .= "３位 ： " . $male_2 . PHP_EOL;
                }
            }
        }
        $message .= PHP_EOL . "【 2013年私に最もイイネ！をつけた女 】" . PHP_EOL;
        if (!empty($female_0)) {
            $message .= "１位 ： " . $female_0 . PHP_EOL;
            if (!empty($female_1)) {
                $message .= "２位 ： " . $female_1 . PHP_EOL;
                if (!empty($female_2)) {
                    $message .= "３位 ： " . $female_2 . PHP_EOL;
                }
            }
        }
        $message .= PHP_EOL . "あなたも、2013年の新聞作ってみてください" . PHP_EOL;
        $message .= ROOT_URL;
        $attachment = array(
            'picture' => $img_wall,
            'link' => ROOT_URL,
            'name' => $user['name'] . 'さんが、2013年新聞を作成した。',
            'description' => 'あなたも、2013年の自分新聞作りませんか？',
            'message' => $message
        );
        $result = FB::post_wall($attachment);
        if ($result) {
            echo "<script>alert('画像を投稿しました！');</script>";
        } else {
            $fb_error = FB::get_lastest_error();
            $this->log_err(" when posting to Facebook: " . $fb_error);
        }
        unlink($proftmpPath);
    }

    public function postwall() {
        $this->autoRender = FALSE;
        if ($this->request->isPost()) {
            //$id = $this->request->data['id'];
            $username = FB::get_me();

            $album_id = FB::create_album("", "2013年私の新聞");
            $att = array(
                "message" => "",
                "image" => IMAGE_UPLOAD_PATH . "result/newspaper_" . $username['id'] . ".jpg"
            );

            FB::upload_photo($att, $album_id);

            return TRUE;
        }
    }

}

?>
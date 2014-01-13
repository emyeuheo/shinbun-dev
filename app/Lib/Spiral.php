<?php

/*
 * This is a wraping class for spiral db
 */

class Spiral {

// サービス用のURL (ロケータから取得できます)https://ctr34.smp.ne.jp/
    private $api_url;
// スパイラルの操作画面で発行したトークンを設定します。
    private $token;
    private $secret;
// サンプルで登録するメールアドレス
    private $db_name;
    private static $instance = null;

    public static function getInstance($api_url = SPIRAL_API_URL, $token = SPIRAL_TOKEN, $secret = SPIRAL_SECRET, $db_name = SPIRAL_DB_NAME) {
        if (Spiral::$instance == null) {
            Spiral::$instance = new Spiral($api_url, $token, $secret, $db_name);
        }

        return Spiral::$instance;
    }

    private function __construct($api_url = SPIRAL_API_URL, $token = SPIRAL_TOKEN, $secret = SPIRAL_SECRET, $db_name = SPIRAL_DB_NAME) {
        $this->api_url = $api_url;
        $this->token = $token;
        $this->secret = $secret;
        $this->db_name = $db_name;
    }

    // -----------------------------------------------------------------------------
    // 登録
    // -----------------------------------------------------------------------------

    /* INSERT
      @access public
     */
    public function insert($str_array) {
        // API用のHTTPヘッダ
        $api_headers = array(
            "X-SPIRAL-API: database/insert/request",
            "Content-Type: application/json; charset=UTF-8",
        );

        // 送信するJSONデータを作成
        $parameters = array();
        $parameters["spiral_api_token"] = $this->token;       //トークン
        $parameters["db_title"] = $this->db_name; //DBのタイトル
        $parameters["passkey"] = time();       //エポック秒
        // 登録したいデータを設定します（１件分）
        // 改行は改行コード("\n")を指定します。JSONデータは "\n" の文字へエスケープされます。
        //$parameters["data"]=$str_array();
        $parameters["data"] = array();
        array_splice($parameters["data"], count($parameters["data"]), 0, $str_array);

        // 署名を付けます
        $key = $parameters["spiral_api_token"] . "&" . $parameters["passkey"];
        $parameters["signature"] = hash_hmac('sha1', $key, $this->secret, false);
        //print_r($parameters);
        // 送信用のJSONデータを作成します。
        $json = json_encode($parameters);

        // curlライブラリを使って送信します。
        $curl = curl_init($this->api_url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $api_headers);
        curl_exec($curl);

        // エラーがあればエラー内容を表示
        if (curl_errno($curl))
            echo curl_error($curl);

        $response = curl_multi_getcontent($curl);

        curl_close($curl);

        // 画面に表示
        // 配列にしたい時は json_decode($response, true); とします。
        //echo "$response\n\n";
        return json_decode($response, true);
    }

    /* UPDATE
      @access public
     */

    function update($condition, $data) {
        // API用のHTTPヘッダ
        $api_headers = array(
            "X-SPIRAL-API: database/update/request",
            "Content-Type: application/json; charset=UTF-8",
        );


        // 送信するJSONデータを作成
        $parameters = array();
        $parameters["spiral_api_token"] = $this->token;       //トークン
        $parameters["db_title"] = $this->db_name; //DBのタイトル
        $parameters["passkey"] = time();       //エポック秒

        $parameters["search_condition"] = array();
        array_splice($parameters["search_condition"], count($parameters["search_condition"]), 0, $condition);
        // 登録したいデータを設定します（１件分）
        // 改行は改行コード("\n")を指定します。JSONデータは "\n" の文字へエスケープされます。
        $parameters["data"] = array();
        array_splice($parameters["data"], count($parameters["data"]), 0, $data);

        // 署名を付けます
        $key = $parameters["spiral_api_token"] . "&" . $parameters["passkey"];
        $parameters["signature"] = hash_hmac('sha1', $key, $this->secret, false);

        // 送信用のJSONデータを作成します。
        $json = json_encode($parameters);

        // curlライブラリを使って送信します。
        $curl = curl_init($this->api_url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $api_headers);
        curl_exec($curl);

        // エラーがあればエラー内容を表示
        if (curl_errno($curl))
            echo curl_error($curl);

        $response = curl_multi_getcontent($curl);

        curl_close($curl);
        return json_decode($response, true);
    }

    /* DELETE
      @access public
     */

    function delete($search_condition) {
        // API用のHTTPヘッダ
        $api_headers = array(
            "X-SPIRAL-API: database/delete/request",
            "Content-Type: application/json; charset=UTF-8",
        );


        // 送信するJSONデータを作成
        $parameters = array();
        $parameters["spiral_api_token"] = $this->token;       //トークン
        $parameters["db_title"] = $this->db_name; //DBのタイトル
        $parameters["passkey"] = time();       //エポック秒

        $parameters["search_condition"] = array();
        array_splice($parameters["search_condition"], count($parameters["search_condition"]), 0, $search_condition);

        // 署名を付けます
        $key = $parameters["spiral_api_token"] . "&" . $parameters["passkey"];
        $parameters["signature"] = hash_hmac('sha1', $key, $this->secret, false);


        // 送信用のJSONデータを作成します。
        $json = json_encode($parameters);

        // curlライブラリを使って送信します。
        $curl = curl_init($this->api_url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $api_headers);
        curl_exec($curl);

        // エラーがあればエラー内容を表示
        if (curl_errno($curl))
            echo curl_error($curl);

        $response = curl_multi_getcontent($curl);

        curl_close($curl);

        return json_decode($response, true);
    }

// -----------------------------------------------------------------------------
// SELECT
// -----------------------------------------------------------------------------
    /* SELECT
      @access public
     */
    function select($select_columns, $search_condition) {

        //$spiral_fields = GlobalVar::get_spiral_field_list();
        //$spiral_fields = array_keys($spiral_fields);
        // API用のHTTPヘッダ
        $api_headers = array(
            "X-SPIRAL-API: database/select/request",
            "Content-Type: application/json; charset=UTF-8",
        );


        // 送信するJSONデータを作成
        $parameters = array();
        $parameters["spiral_api_token"] = $this->token;       //トークン
        $parameters["db_title"] = $this->db_name; //DBのタイトル
        $parameters["passkey"] = time();       //エポック秒
        // 表示カラム名
        $parameters["select_columns"] = array();
        array_splice($parameters["select_columns"], count($parameters["select_columns"]), 0, $select_columns);



        // 検索条件
        $parameters["search_condition"] = array();
        array_splice($parameters["search_condition"], count($parameters["search_condition"]), 0, $search_condition);

        // 署名を付けます
        $key = $parameters["spiral_api_token"] . "&" . $parameters["passkey"];
        $parameters["signature"] = hash_hmac('sha1', $key, $this->secret, false);

        //print_r($parameters);
//        $this->log_err("Spiral:db-select - token" . $this->token . "dbname:" . $this->db_name . "url-" . $this->api_url . "sercret-" . $this->secret, 0);
        // 送信用のJSONデータを作成します。
        $json = json_encode($parameters);

        // curlライブラリを使って送信します。
        $curl = curl_init($this->api_url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $api_headers);
        curl_exec($curl);
        if (curl_errno($curl))
            echo curl_error($curl);


        $response = curl_multi_getcontent($curl);
        $temp = json_decode($response, true);

        //print_r($response);
        //print_r($result);
        curl_close($curl);

        $result = array();
        if ($temp["message"] == "OK") {
            $result["message"] = $temp["message"];
            $result["count"] = $temp["count"];
            $result["data"] = array();
            //print_r($temp["data"]);
            foreach ($temp["data"] as $k => $row) {
                //print_r($row);
                // print_r($select_columns);
                foreach ($select_columns as $f => $field) {
                    $result["data"][$k][$field] = $row[$f];
                    
                }
            }
        } else {
            $result["message"] = $temp["message"];
        }

        // 画面に表示
        // 配列にしたい時は json_decode($response, true); とします。
        //echo "$response\n\n";
        return $result;
    }

    /*
      Dump
     */

    function dump($select_columns, $search_condition, $file) {
        $fp = fopen($file . ".dum", "w+");
        //$spiral_fields = GlobalVar::get_spiral_field_list();
        //$spiral_fields = array_keys($spiral_fields);
        // API用のHTTPヘッダ
        $api_headers = array(
            "X-SPIRAL-API: database/select/request",
            "Content-Type: application/json; charset=UTF-8",
        );


        // 送信するJSONデータを作成
        $parameters = array();
        $parameters["spiral_api_token"] = $this->token;       //トークン
        $parameters["db_title"] = $this->db_name; //DBのタイトル
        $parameters["passkey"] = time();       //エポック秒
        // 表示カラム名
        $parameters["select_columns"] = array();
        array_splice($parameters["select_columns"], count($parameters["select_columns"]), 0, $select_columns);



        // 検索条件
        $parameters["search_condition"] = array();
        array_splice($parameters["search_condition"], count($parameters["search_condition"]), 0, $search_condition);

        // 署名を付けます
        $key = $parameters["spiral_api_token"] . "&" . $parameters["passkey"];
        $parameters["signature"] = hash_hmac('sha1', $key, $this->secret, false);

        //print_r($parameters);
//        $this->log_err("Spiral:db-select - token".$this->token."dbname:".$this->db_name."url-".$this->api_url."sercret-".$this->secret,0);
        // 送信用のJSONデータを作成します。
        $json = json_encode($parameters);

        // curlライブラリを使って送信します。
        $curl = curl_init($this->api_url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $api_headers);
        curl_exec($curl);
        if (curl_errno($curl))
            echo curl_error($curl);


        $response = curl_multi_getcontent($curl);
        $temp = json_decode($response, true);

        //print_r($response);
        //print_r($result);
        curl_close($curl);

        $result = array();
        $result["message"] = $temp["message"];
        $result["count"] = $temp["count"];
        $result["data"] = array();
        // db title
        foreach ($select_columns as $c => $col) {
            $col_txt.=$col . "\t";
        }
        fputs($fp, trim($col_txt) . "\n");

        foreach ($temp["data"] as $k => $row) {
            $row_str = "";
            //print_r($row);
            foreach ($select_columns as $f => $field) {
                $result["data"][$k][$field] = $row[$f];
                if ($row[$f] == "")
                    $row_str.="empty_field\t";
                else
                    $row_str.=$row[$f] . "\t";
            }
            fputs($fp, trim($row_str) . "\n");
            $row_str = "";
        }
        //file

        fclose($fp);

        return $result;
    }

    /*
     * [ 概要 ]
     * - Thanksメールを送信する 
     *   フィールド：
     *             No1 フィールド名　　：メールアドレス
     *                 フィールドタイプ：メールアドレス（大・小文字を無視）
     *                 差替えキーワード：mail
     *
     *             No2 フィールド名　　：テキストエリア
     *                 フィールドタイプ：テキストエリア(256bytes)
     *                 差替えキーワード：text
     *
     * 1. THANKS配信登録で使うため、「Webグループ」「フォーム管理」で、登録フォームを作成します。
     *    実際にフォームは使いませんのでダミーの設定ということになります。
     *
     * 2. THANKS配信登録で使うため、「メールグループ」「リスト作成」で、
     *    適当な設定でリストを追加します。
     *
     * 3. THANKS配信登録で使うため、「メールグループ」「封筒製作：直接入力」で、
     *    封筒を追加します。どの封筒形式でもかまいません。
     *    
     * 4. スパイラルの操作画面の「メールグループ」「THANKS配信登録」で、
     *    上記で作成した登録フォームに対して配信設定を追加します。
     *
     */

    public function deliver_thanks($rule_id = SPIRAL_DELIVERY_THANKS_RULE_ID, $id_arr = SPIRAL_DELIVERY_THANKS_ID) {
        if (is_array($id_arr)) {
            $result_arr = array();
            foreach ($id_arr as $id => $val) {
                $result_arr[] = $this->thanks(SPIRAL_DELIVERY_THANKS_RULE_ID, $id);
            }
            return $result_arr;
        } else {
            return $this->thanks(SPIRAL_DELIVERY_THANKS_RULE_ID, $id_arr);
        }
    }

    private function thanks($rule_id = null, $id = null) {
        if ($id == null) {
            return null;
        }

        // API用のHTTPヘッダ
        $api_headers = array(
            "X-SPIRAL-API: deliver_thanks/send/request",
            "Content-Type: application/json; charset=UTF-8",
        );

        // 送信するJSONデータを作成
        $parameters = array();
        $parameters["spiral_api_token"] = $this->token;
        $parameters["rule_id"] = $rule_id;   //サンキューメールの配信ルールID
        $parameters["id"] = $id;   //レコードのid
        $parameters["passkey"] = time();

        // 署名を付けます
        $key = $parameters["spiral_api_token"] . "&" . $parameters["passkey"];
        $parameters["signature"] = hash_hmac('sha1', $key, $this->secret, false);

        // 送信用のJSONデータを作成します。
        $json = json_encode($parameters);

        // curlライブラリを使って送信します。
        $curl = curl_init($this->api_url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $api_headers);
        curl_exec($curl);

        // エラーがあればエラー内容を表示
        if (curl_errno($curl))
            echo curl_error($curl);

        $response = curl_multi_getcontent($curl);

        curl_close($curl);

        return json_decode($response, true);
    }

}
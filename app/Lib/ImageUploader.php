<?php
class ImageUploader{
    protected $destination;
    protected $upload_data;
    protected $new_name;
    protected $check_pattern;
    protected $error_message;
    protected $image_file_type_arr = array(
        2 => 'jpg',
        3 => 'png',
    );
    protected $img_ext;
	
    function __construct($destination, $upload_data, $new_name, $check_pattern = array( 'ext' => array( 2 => 'jpg', 3 => 'png'))){
        $this->destination = $destination;
        $this->upload_data = $upload_data;
        $this->new_name = $new_name;
        $this->check_pattern = $check_pattern;
    }
    
    public function get_error_message(){
        return $this->error_message;
    }
    public function get_image_ext(){
        return $this->img_ext;
    }
    
    public function upload(){
        if (!is_dir($this->destination)){
            $this->error_message = "画像のフォルダーが見つかりません。";
            return false;
        }
/*        
        upload_data:
            array ( 'image' => array ( 'name' => 'img.png', 
                'type' => 'image/png', 
                'tmp_name' => 'C:\\Windows\\Temp\\php471.tmp', 
                'error' => 0, 
                'size' => 73086, ), 
              )
*/      
        if (!is_uploaded_file($this->upload_data['tmp_name'])){
            $this->error_message = "アップロードファイルが見つかりません。";
            return false;
        }
        
        list($imgWidth, $imgHeight, $imgType, $imgAttr) = getimagesize($this->upload_data['tmp_name']);
        
        if ( !isset($this->check_pattern['ext'][$imgType]) || $this->image_file_type_arr[$imgType] != $this->check_pattern['ext'][$imgType]){                    // 拡張仕様チェック
            $this->error_message = "画像拡張は誤りがあります。 拡張： ".$this->check_pattern['ext'][$imgType];
            return false;
        }
        
        $this->img_ext = $this->check_pattern['ext'][$imgType];
        if (isset($this->check_pattern['size'])){
            $size = explode('x', $this->check_pattern['size']);
            if ($imgWidth > $size[0] || $imgHeight > $size[1]){    // サイズ仕様チェック
                $this->error_message = "画像サイズは誤りがあります。<br/>最大：".$this->check_pattern['size']."px";
                return false;
            }
        }
 
        $new_filename = $this->destination.$this->new_name.'.'.$this->check_pattern['ext'][$imgType];
        $success = move_uploaded_file($this->upload_data['tmp_name'], $new_filename);
                                         
        chmod($new_filename, 0664);
        
        return $success;
    }
}
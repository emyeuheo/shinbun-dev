<?php
class Lang{
    /**
    * Description: This function is called when program is booted. 
    *              It loads all language files into global arrays for searching
    */
    public static function load($key){
        global $lang_group_arr;
        if (!file_exists(LANG_PATH.$key) || !is_dir(LANG_PATH.$key)){
            Lang::add_error(__FUNCTION__, 'Lang directory not found.');
            return null;
        }
        
        if ($handle = opendir(LANG_PATH.$key)) {		
            while (false !== ($entry = readdir($handle))) {
                if (preg_match("/^(?:[a-zA-Z0-9_]+)(?:\\.php)?$/", $entry, $matches)){
                    $lang_group_key = str_replace('.php', '', $entry);
                    $lang_group_arr[$lang_group_key] = @include LANG_PATH.$key.'/'.$entry;
                    if (!is_array($lang_group_arr[$lang_group_key])){
                        unset($lang_group_arr[$lang_group_key]);
                        Lang::add_error(__FUNCTION__, 'Lang entry is wrong.');
                    }
                }
            }
            closedir($handle);
        } else {
            Lang::add_error(__FUNCTION__, 'Could not handle lang setting.');
            return null;
        }
        
        return $lang_group_arr;
    }
    
    public static function get($lang_key, $lang_group = null){
        global $lang_group_arr;
        // Look into specified language files
        if (!empty($lang_group)){
            if (!is_array($lang_group)){
                $lang_group = array($lang_group);
            }
            
            foreach($lang_group as $key => $group){
                if (isset($lang_group_arr[$group])){
                    if (isset($lang_group_arr[$group][$lang_key])){
                        return $lang_group_arr[$group][$lang_key];
                    }
                }
            }
        }
        
        // Search entire language files
        foreach($lang_group_arr as $key => $lang_set){
            if (isset($lang_set[$lang_key])){
                return $lang_set[$lang_key];
            }
        }
        
        return null;
    }

    public static function get_lastest_error(){
        global $lang_error_arr;
        if (empty($lang_error_arr)){
            return 0;
        }
         
        return end($lang_error_arr);	
    }

    public static function get_error($key){
        global $lang_error_arr;	
        return isset($lang_error_arr[$key])?$lang_error_arr[$key]:0;
    }

    public static function add_error($key, $error_msg){
        global $lang_error_arr;
        if (isset($lang_error_arr[$key])){
            unset($lang_error_arr[$key]);
        }
        $lang_error_arr[$key] = $error_msg;
    }
}


/*
	Omit PHP closing tag to help avoid accidental output
*/
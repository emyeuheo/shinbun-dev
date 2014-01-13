<?php
/*
 Usage example:
$check_patterns = array('type' => 'VARCHAR', 'require' => true);
$check_patterns = array('type' => 'INT', 'require' => true, 'min' => 0, 'max' => 255);


$val = something to check;
$check = Validator::check($val, $check_patterns);
if ($check != null){
// got error
} else {
// OK
}
*/

class Validator{	
	public static function check($subject, $pattern){
		// check require if set
		if (isset($pattern['require']) && $pattern['require']){
			if (empty($subject) || !isset($subject)){
				return 'require';
			}
		}

		$check_type = $pattern['type'];


		// check pattern
		if (!isset($check_type) || empty($check_type)){
			return 'wrong_pattern : unknown type';
		}
		if (isset($pattern['min']) && !empty($pattern['min'])){
			if (Validator::check($pattern['min'], array('type' => 'INT'))) {
				return 'wrong_pattern : min must be INT';
			}
		}

		if (isset($pattern['max']) && !empty($pattern['max'])){
			if (Validator::check($pattern['max'], array('type' => 'INT'))) {
				return 'wrong_pattern : max must be INT';
			}
		}

		if (isset($pattern['M']) && !empty($pattern['M'])){
			if (Validator::check($pattern['M'], array('type' => 'INT'))) {
				return 'wrong_pattern : M of DECIMAL must be INT';
			}
		}

		if (isset($pattern['D']) && !empty($pattern['D'])){
			if (Validator::check($pattern['D'], array('type' => 'INT'))) {
				return 'wrong_pattern : D of DECIMAL must be INT';
			}
		}

		if (isset($pattern['limit']) && !empty($pattern['limit'])){
			if (Validator::check($pattern['limit'], array('type' => 'INT'))) {
				return 'wrong_pattern : limit of NUMERIC must be INT';
			}
		}

		// check subject
		if ( get_magic_quotes_gpc() ) {
			$val = stripslashes($subject);
		}
		else{
			$val = $subject;
		}

		if ($check_type == 'VARCHAR'){
			$val = strip_tags($val);
			if (isset($pattern['min']) && !empty($pattern['min'])){
				$min = $pattern['min'];
			} else {
				$min = 0;
			}

			if (isset($pattern['max']) && !empty($pattern['max'])){
				$max = $pattern['max'];
			} else {
				$max = 2147483647;
			}
			
			if ( strlen($val) < $min || strlen($val) > $max) {
				return 'VARCHAR: min | max';
			}
		} elseif ($check_type == 'TEXT'){
			$val = strip_tags($val);
			if (isset($pattern['min']) && !empty($pattern['min'])){
				$min = $pattern['min'];
			} else {
				$min = 0;
			}

			if (isset($pattern['max']) && !empty($pattern['max'])){
				$max = $pattern['max'];
			} else {
				$max = 65535;
			}

			if ( strlen($val) < $min || strlen($val) > $max) {
				return 'TEXT: min | max';
			}

		} elseif ($check_type == 'HTML'){

		} elseif ($check_type == 'INT'){
			if (isset($pattern['signed']) && $pattern['signed']) {
				$min = -2147483648;
				$max =  2147483647;
			} else {
				$min = 0;
				$max = 4294967295;
			}
			if ( preg_match("/-/", substr($val, 1)) || preg_match("/[^0-9\-]/", $val)) {
				return '!INT';
			}
			if ($val < $min || $val > $max){
				return 'INT: min | max';
			}
		} elseif ($check_type == 'TINYINT'){
			if (isset($pattern['signed']) && $pattern['signed'] ) {
				$min = -128;
				$max =  127;
			} else {
				$min = 0;
				$max = 255;
			}
			if ( preg_match("/-/", substr($val, 1)) || preg_match("/[^0-9\-]/", $val)) {
				return '!TINYINT';
			}

			if ($val < $min || $val > $max){
				return 'TINYINT: min | max';
			}
		} elseif ($check_type == 'SMALLINT'){
			if (isset($pattern['signed']) && $pattern['signed'] ) {
				$min = -32768;
				$max =  32767;
			} else {
				$min = 0;
				$max = 65535;
			}
			if ( preg_match("/-/", substr($val, 1)) || preg_match("/[^0-9\-]/", $val)) {
				return '!SMALLINT';
			}

			if ($val < $min || $val > $max){
				return 'SMALLINT: min | max';
			}

		} elseif ($check_type == 'MEDIUMINT'){
			if (isset($pattern['signed']) && $pattern['signed'] ) {
				$min = -8388608;
				$max =  8388607;
			} else {
				$min = 0;
				$max = 16777215;
			}
			if ( preg_match("/-/", substr($val, 1)) || preg_match("/[^0-9\-]/", $val)) {
				return '!MEDIUMINT';
			}
			if ($val < $min || $val > $max){
				return 'MEDIUMINT: min | max';
			}
		} elseif ($check_type == 'NUMERIC'){
			if ( preg_match("/-/", substr($val, 1))
			|| preg_match("/[^0-9\-]/", $val) || $val < 0 ) {
				return '!NUMERIC';
			}
			if ((isset($pattern['limit']) && $val > $pattern['limit'])){
				return 'NUMERIC: limit';
			}
		} elseif ($check_type == 'FLOAT'){
			if ( substr_count($val, ".") > 1
			|| preg_match("/-/", substr($val, 1)) || preg_match("/[^0-9\-\.]/", $val)) {
				return '!FLOAT';
			}

			if (isset($pattern['min']) && !empty($pattern['min'])){
				if ( $val < $pattern['min'] ) {
					return 'FLOAT: min';
				}
			}

			if (isset($pattern['max']) && !empty($pattern['max'])){
				if ( $val > $pattern['max'] ) {
					return 'FLOAT: max';
				}
			}
		} elseif ($check_type == 'DECIMAL'){
			list($tmpM, $tmpD) = explode('.', $val);
			$digitM = strlen($tmpM);
			$digitD = strlen($tmpD);
			if ( substr_count($val, ".") > 1 || preg_match("/-/", substr($val, 1))
			|| preg_match("/[^0-9\-\.]/", $val)) {
				return '!DECIMAL';
			}

			if (isset($pattern['M']) && isset($pattern['D'])){
				if ($digitM > $pattern['M'] || $digitD > $pattern['D'] ){
					return 'DECIMAL: M | D';
				}
			}

		} elseif ($check_type == 'DATETIME'){
			$regs = array(
					"/^\d{4}[\/-]\d{1,2}[\/-]\d{1,2}\s\d{1,2}:\d{1,2}:\d{1,2}/", // 2012-07-12 22:38:57 or 2012/07/12 22:38:57
					);
			$dt_valid = false;
			foreach ($regs as $regex){
				if (preg_match($regex, $val)) {
					$dt_valid = true;
					break;
				}
			}
			if (!$dt_valid) {
				return '!DATETIME';
			}
		}elseif ($check_type == 'TIMEZONE'){
			if(!preg_match("/^[+-](((0[0-9]|1[0-1]):[0-5][0-9])|12:00)$/", $val)){;
				return '!TIMEZONE';
			}
		} elseif ($check_type == 'DATE'){

		} elseif ($check_type == 'URL'){
			if (!preg_match("/^(http(?:s)?\:\/\/[a-zA-Z0-9\-]+(?:\.[a-zA-Z0-9\-]+)*\.[a-zA-Z]{2,6}(?:\/?|(?:\/[\w\-]+)*)(?:\/?|\/\w+\.[a-zA-Z]{2,4}(?:\?[\w]+\=[\w\-]+)?)?(?:\&[\w]+\=[\w\-]+)*)(:[\d]{1,4})?$/",$val)){
				//must have http(s)
				return '!URL';
			}
			if (isset($pattern['min']) && !empty($pattern['min'])){
				if ( strlen($val) < $pattern['min'] ) {
					return 'URL: min';
				}
			}

			if (isset($pattern['max']) && !empty($pattern['max'])){
				if ( strlen($val) > $pattern['max'] ) {
					return 'URL: max';
				}
			}
		} elseif ($check_type == 'MAIL'){
			if ( !preg_match("/^\w[\w-.]*\@[\w-]+(.\w+){1,2}$/", $val)
			|| !checkdnsrr(str_replace("@", "", strrchr($val, "@")), "MX")) {
				return '!MAIL';
			}
			if (isset($pattern['min']) && !empty($pattern['min'])){
				if ( strlen($val) < $pattern['min'] ) {
					return 'MAIL: min';
				}
			}

			if (isset($pattern['max']) && !empty($pattern['max'])){
				if ( strlen($val) > $pattern['max'] ) {
					return 'MAIL: max';
				}
			}
		} elseif ($check_type == 'MOBILE'){
			if (!preg_match("/^[\+]?\d+(\-\d+)*$/",$val)){
				return '!MOBILE';
			}
			if (isset($pattern['min']) && !empty($pattern['min'])){
				if ( strlen($val) < $pattern['min'] ) {
					return 'MOBILE: min';
				}
			}

			if (isset($pattern['max']) && !empty($pattern['max'])){
				if ( strlen($val) > $pattern['max'] ) {
					return 'MOBILE: max';
				}
			}
		}

		return false;
	}
}

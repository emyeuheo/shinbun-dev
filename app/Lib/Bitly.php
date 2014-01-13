<?php

//this class is minified

class Bitly {
    /**
     * @file
     * Simple PHP library for interacting with the v3 bit.ly api (only deals with
     * JSON format, but supports new OAuth endpoints).
     * REQUIREMENTS: PHP, Curl, JSON
     * 
     * @link https://github.com/Falicon/BitlyPHP
     * @author Kevin Marshall <info@falicon.com>
     * @author Robin Monks <devlinks@gmail.com>
     */
    /**
     * The bitlyKey assigned to your bit.ly account. (http://bit.ly/a/account)
     */

    /**
     * Given a longUrl, get the bit.ly shortened version.
     *
     * Example usage:
     * @code
     *   $results = bitly_v3_shorten('http://knowabout.it', 'j.mp');
     * @endcode
     *
     * @param $longUrl
     *   Long URL to be shortened.
     * @param $domain
     *   Uses bit.ly (default), j.mp, or a bit.ly pro domain.
     * @param $x_login
     *   User's login name.
     * @param $x_api_key
     *   User's API key.
     *
     * @return
     *   An associative array containing:
     *   - url: The unique shortened link that should be used, this is a unique
     *     value for the given bit.ly account.
     *   - hash: A bit.ly identifier for long_url which is unique to the given
     *     account.
     *   - global_hash: A bit.ly identifier for long_url which can be used to track
     *     aggregate stats across all matching bit.ly links.
     *   - long_url: An echo back of the longUrl request parameter.
     *   - new_hash: Will be set to 1 if this is the first time this long_url was
     *     shortened by this user. It will also then be added to the user history.
     *
     * @see http://code.google.com/p/bitly-api/wiki/ApiDocumentation#/v3/shorten
     */
    static function bitly_v3_shorten($longUrl, $domain = '', $x_login = '', $x_apiKey = '') {
        $result = array();
        $url = BITLY_OAUTH_API . "shorten?access_token=" . BITLY_KEY . "&longUrl=" . urlencode($longUrl);
        if ($domain != '') {
            $url .= "&domain=" . $domain;
        }
        if ($x_login != '' && $x_apiKey != '') {
            $url .= "&x_login=" . $x_login . "&x_apiKey=" . $x_apiKey;
        }
        $output = json_decode(self::bitly_get_curl($url));
        if (isset($output->{'data'}->{'hash'})) {
            $result['url'] = $output->{'data'}->{'url'};
            $result['hash'] = $output->{'data'}->{'hash'};
            $result['global_hash'] = $output->{'data'}->{'global_hash'};
            $result['long_url'] = $output->{'data'}->{'long_url'};
            $result['new_hash'] = $output->{'data'}->{'new_hash'};
        }
        return $result;
    }

    /**
     * Make a GET call to the bit.ly API.
     *
     * @param $uri
     *   URI to call.
     */
    static function bitly_get_curl($uri) {
        $output = "";
        try {
            $ch = curl_init($uri);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 4);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $output = curl_exec($ch);
        } catch (Exception $e) {
            $backtrace = debug_backtrace();
            $last = $backtrace[0];
            CakeLog::error("[" . date_default_timezone_get() . "] " . basename($last['file']) . " " . $last['line'] . " bitly exception: " . $e->getMessage());
        }
        return $output;
    }

}

?>

<?php
/**************************
* The UserAgentInfo class encapsulates information about
* a browser's connection to your web site.
* The object's methods return 1 for true, or 0 for false.
***************************/
class UserAgentInfo{
    // Stores some info about the browser and device.
    var $useragent = "";

    // Stores info about what content formats the browser can display.
    var $httpaccept = "";

    // A long list of strings which provide clues
    //   about devices and capabilities.
    var $devices = array('iphone' => 'iphone', 'ipod' => 'ipod', 'ipad' => 'ipad', 'android' => 'android');
    var $oss = array('MAC OS X', 'Windows', 'Win 9x', 'FreeBSD', 'SunOS', 'Linux', 'OpenBSD', 'windows nt', 'macintosh');
    var $browsers = array('MSIE' => 'IE',
                         'Firefox' => 'Firefox',
                          'Chrome' => 'Chrome',
                            'Safari' => 'Safari'
    );

    // The constructor. Initializes several default variables.
    function UserAgentInfo(){
        $this->useragent = strtolower(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT']:'');
        $this->httpaccept = strtolower(isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '');
    }

    function readUserAgent($user_agent){
        $this->useragent = $user_agent;
    }
    // Returns the contents of the User Agent value, in lower case.
    function getUserAgent(){
       return $this->useragent;
    }

    // Detects if the current device is an iPhone.
    function isIphone(){
      return (stripos($this->useragent, $this->devices['iphone']) > 0);
    }


    // Detects if the current device is an iPad.
    function isIpad(){
      return (stripos($this->useragent, $this->devices['ipad']) > 0);
    }

    // Detects if the current device is an iPod.
    function isIpod(){
      return (stripos($this->useragent, $this->devices['ipod']) > 0);
    }

    // Detects if the current device is an Android.
    function isAndroid(){
      return (stripos($this->useragent, $this->devices['android']) > 0);
    }

    function isIE(){
        return (stripos($this->useragent, $this->browsers['MSIE']) > 0);
    }

    function isFireFox(){
        return (stripos($this->useragent, $this->browsers['Firefox']) > 0);
    }

    function isChrome(){
    return (stripos($this->useragent, $this->browsers['Chrome']) > 0);
    }

    function isSafari(){
    return ( stripos($this->useragent, $this->browsers['Chrome']) == 0 &&
            stripos($this->useragent, $this->browsers['Safari']) > 0 );
    }

    function isSP(){
        foreach($this->devices as $key => $device){
            if (stripos($this->useragent, $device) > -1){
                return true;
            }
        }
        return false;
    }

    function isPC(){
        foreach ($this->oss as $os){
            if (strpos($this->useragent, $os) > -1){
                return true;
            }
        }

        return false;
    }
}
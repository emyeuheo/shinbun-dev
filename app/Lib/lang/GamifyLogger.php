<?php
require_once (VENDOR_PATH.'log4php/Logger.php');
class GamifyLogger{
    private static $logger = null;
    
	public static function getLogger($support_mail_list = array()){
        if (GamifyLogger::$logger == null){
            $datetime = date("Ymd"); //get current date time
            $filename = LOG_PATH.'quiz_'.$datetime.'.log';
            
            $appenders['log2file'] = array(
                                'class' => 'LoggerAppenderRollingFile',//writes logging events to a specified file.
                                // The file is rolled over after a specified size has been reached.
                                //http://logging.apache.org/log4php/docs/appenders/rolling-file.html
                                'layout' => array(
                                        'class' => 'LoggerLayoutTTCC',
                                ),
                                'params' => array(
                                        'file' => $filename,
                                        'maxFileSize' => '5MB', // TODO
                                        'maxBackupIndex' => 5,
                                ),
                        );
            if (is_array($support_mail_list) && !empty($support_mail_list)){
                $appenders['log2mail'] = array(
                                'class' => 'LoggerAppenderMailEvent',
                                'layout' => array(
                                        'class' => 'LoggerLayoutTTCC',
                                ),
                                'params' => array(
                                        'to' => implode(',', $support_mail_list), // TODO
                                        'from' => DIR_NAME.'@gmail.com',
                                        'subject' => DIR_NAME.'_log notification'
                                ),
                        );
                $rootLogger = array('appenders' => array('log2file', 'log2mail'),);
            } else {
                $rootLogger = array('appenders' => array('log2file',),);
            }
            
            Logger::configure(array(
                'appenders' => $appenders,
                'rootLogger' => $rootLogger,
            ));            
            GamifyLogger::$logger = Logger::getLogger(DIR_NAME);
		}
        
		return GamifyLogger::$logger;
	}
}
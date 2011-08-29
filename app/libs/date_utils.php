<?php

class DateUtils {
    
    /**
     * Get the offset from a timezone to GMT
     * @param $tzName
     */
    public static function GetTimezoneOffset($tzName){
        $tzOffset = 0;
        if($tzName!=null){
            $dateTimeZoneDisplay = new DateTimeZone($tzName);
            $dateTimeDisplay = new DateTime("now", $dateTimeZoneDisplay);
            $tzOffset = $dateTimeZoneDisplay->getOffset($dateTimeDisplay);
        }
        return $tzOffset;
    }    
    
    /**
     * Copied from http://www.php.net/manual/en/function.date.php#75757
     */
    public static function TimestampToDateRfc3339($timestamp=0) {
        if (!$timestamp) {
            $timestamp = time();
        }
        $date = date('Y-m-d\TH:i:s', $timestamp);
        $matches = array();
        if (preg_match('/^([\-+])(\d{2})(\d{2})$/', date('O', $timestamp), $matches)) {
            $date .= $matches[1].$matches[2].':'.$matches[3];
        } else {
            $date .= 'Z';
        }
        return $date;
    }
    
} 

?>
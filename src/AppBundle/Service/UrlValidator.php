<?php

namespace AppBundle\Service;

class UrlValidator
{
    /**
     * Validate url by http status code
     * @param string $url
     * @return bool
     */
    public static function isValidUrl($url){
        $file_headers = @get_headers($url);
        if(!$file_headers || preg_match("/HTTP.* 404/i", $file_headers[0])) {
            $result = false;
        }else{
            $result = true;
        }
        return $result;
    }
}
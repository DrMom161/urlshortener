<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 18.02.17
 * Time: 13:31
 */

namespace AppBundle\Service;


class ResponseBuilder
{
    /**
     * List of errors
     * @var array
     */
    private static $errors = array();
    /**
     * Additional data of response
     * @var array
     */
    private static $data = array();
    /**
     * Add error to list
     * @param string $errorText
     */
    public static function addError($errorText){
        self::$errors[] = $errorText;
    }

    /**
     * Add data to list
     * @param string $key
     * @param string $value
     */
    public static function addData($key, $value){
        self::$data[$key] = $value;
    }

    /**
     * Get info about errors existence
     * @return bool
     */
    public static function hasErrors(){
        return (bool)self::$errors;
    }

    /**
     * Generate response in specific format
     * @return array (
     *                  hasError boolean - errors existence
     *                  [errors array - list of errors messages]
     *              )
     */
    public static function getResponse()
    {
        $hasError = self::hasErrors();
        $response = array('hasError' => $hasError);

        if($hasError){
            $response['errors'] = self::$errors;
        }else{
            $response['data'] = self::$data;
        }

        return $response;
    }
}
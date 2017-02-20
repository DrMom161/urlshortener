<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseBuilder
{
    /**
     * List of errors
     * @var array
     */
    private $errors = array();
    /**
     * Additional data of response
     * @var array
     */
    private $data = array();

    /**
     * Add error to list
     * @param string $errorText
     */
    public function addError($errorText)
    {
        $this->errors[] = (string)$errorText;
    }

    /**
     * Add data to list
     * @param string $key
     * @param string $value
     */
    public function addData($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Get info about errors existence
     * @return bool
     */
    public function hasErrors()
    {
        return (bool)$this->errors;
    }

    /**
     * Get errors list
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Generate response in specific format
     * @return array (
     *                  hasError boolean - errors existence
     *                  [errors array - list of errors messages]
     *              )
     */
    public function getResponse()
    {
        $hasError = $this->hasErrors();
        $response = array('hasError' => $hasError);

        if ($hasError) {
            $response['errors'] = $this->errors;
        } else {
            $response['data'] = $this->data;
        }

        return $response;
    }

    /**
     * Generate json response from own data
     * @return JsonResponse
     */
    public function getJsonResponse()
    {
        $response = new JsonResponse();
        $response->setData($this->getResponse());
        return $response;
    }
}
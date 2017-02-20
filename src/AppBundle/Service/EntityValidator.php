<?php

namespace AppBundle\Service;

use Symfony\Component\Validator\Validator\RecursiveValidator;

class EntityValidator
{
    /**
     * @var RecursiveValidator
     */
    private $validator;

    public function __construct($validator)
    {
        $this->validator = $validator;
    }

    /**
     * Validate entity
     * @param object $entityObject
     * @param ResponseBuilder $responseBuilder
     */
    public function validate($entityObject, ResponseBuilder &$responseBuilder)
    {
        $errors = $this->validator->validate($entityObject);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $responseBuilder->addError($error->getMessage());
            }
        }
    }
}
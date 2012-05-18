<?php

namespace EasyCSV;

class ValidationException extends Exception
{

    /**
     *
     * @var array
     */
    private $errors;

    /**
     *
     * @param string $message
     * @param array $errors
     */
    public function __construct($message, array $errors){
        parent::__construct($message);
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function getErrors(){
       return $this->errors;
    }

}
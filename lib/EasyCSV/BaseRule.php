<?php

namespace EasyCSV;

/**
 *
 * @author chente
 *
 */
abstract class BaseRule implements Rule
{

    /**
     *
     * @var boolean
     */
    protected $isRequired;

    /**
     *
     * @var string
     */
    protected $invalidMessage;

    /**
     *
     * @var string
     */
    protected $requiredMessage;

    /**
     *
     * @param boolean $isRequired
     * @param string $invalidMessage
     * @param string $requiredMessage
     */
    public function __construct($isRequired, $invalidMessage, $requiredMessage){
        $this->isRequired = $isRequired;
        $this->invalidMessage = $invalidMessage;
        $this->requiredMessage = $requiredMessage;
    }

    /**
     * @return boolean
     */
    public function isRequired(){
        return $this->isRequired;
    }

    /**
     *
     * @param int $lineNumber
     * @param string $field
     * @param mixed $value
     * @return string
     */
    public function getRequiredMessage($lineNumber, $field, $value){
        return $lineNumber . ' @ '. str_replace(array('%field%', '%value%'), array($field, $value), $this->requiredMessage);
    }

    /**
     *
     * @param int $lineNumber
     * @param string $field
     * @param mixed $value
     * @return string
     */
    public function getErrorMessage($lineNumber, $field, $value){
        return $lineNumber . ' @ '. str_replace(array('%field%', '%value%'), array($field, $value), $this->invalidMessage);
    }

}
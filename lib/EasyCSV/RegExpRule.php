<?php

namespace EasyCSV;

/**
 *
 * @author chente
 *
 */
class RegExpRule implements Rule
{

    /**
     *
     * @var boolean
     */
    private $isRequired;

    /**
     *
     * @var string
     */
    private $regexp;

    /**
     *
     * @var string
     */
    private $invalidMessage;

    /**
     *
     * @var string
     */
    private $requiredMessage;

    /**
     *
     * @param string $regexp
     * @param boolean $isRequired
     * @param string $invalidMessage
     * @param string $requiredMessage
     */
    public function __construct($regexp, $isRequired, $invalidMessage, $requiredMessage){
        $this->regexp = $regexp;
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

    /**
     * @return boolean
     */
    public function isValid($value){
        return preg_match($this->regexp, $value);
    }

}
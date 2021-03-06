<?php

namespace EasyCSV;

/**
 *
 * @author chente
 *
 */
class RegExpRule extends BaseRule
{

    /**
     *
     * @var string
     */
    private $regexp;

    /**
     *
     * @param string $regexp
     * @param boolean $isRequired
     * @param string $invalidMessage
     * @param string $requiredMessage
     */
    public function __construct($regexp, $isRequired, $invalidMessage, $requiredMessage){
        $this->regexp = $regexp;
        parent::__construct($isRequired, $invalidMessage, $requiredMessage);
    }

    /**
     * @return boolean
     */
    public function isValid($value){
        return preg_match($this->regexp, $value);
    }

}
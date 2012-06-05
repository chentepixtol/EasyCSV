<?php

namespace EasyCSV;

/**
 *
 * @author chente
 *
 */
class CallableRule extends BaseRule
{

    /**
     *
     * @var string
     */
    private $callable;

    /**
     *
     * @param string $regexp
     * @param boolean $isRequired
     * @param string $invalidMessage
     * @param string $requiredMessage
     */
    public function __construct($callable, $isRequired, $invalidMessage, $requiredMessage){
        if( !is_callable($callable) ){
            throw new Exception("El callable no es valido");
        }
        $this->callable = $callable;
        parent::__construct($isRequired, $invalidMessage, $requiredMessage);
    }

    /**
     * @return boolean
     */
    public function isValid($value){
        return (boolean) call_user_func_array($this->callable, array($value));
    }

}
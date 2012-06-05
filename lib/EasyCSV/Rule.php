<?php

namespace EasyCSV;

/**
 *
 * @author chente
 *
 */
interface Rule
{

    /**
     * @return boolean
     */
    function isRequired();

    /**
     *
     * @param int $lineNumber
     * @param string $field
     * @param mixed $value
     * @return string
     */
    function getRequiredMessage($lineNumber, $field, $value);

    /**
     *
     * @param int $lineNumber
     * @param string $field
     * @param mixed $value
     * @return string
     */
    function getErrorMessage($lineNumber, $field, $value);

    /**
     * @param mixed $value
     * @return boolean
     */
    function isValid($value);

}
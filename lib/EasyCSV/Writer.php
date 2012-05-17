<?php

namespace EasyCSV;

/**
 *
 * @author chente
 */
class Writer extends AbstractBase
{

    /**
     *
     * @param string $path
     * @param string $mode
     */
    public function __construct($path, $mode = 'r+'){
        if ( ! file_exists($path)) {
            touch($path);
        }
        parent::__construct($path, $mode);
    }

    /**
     *
     * @param array $row
     * @return number
     */
    public function writeRow($row)
    {
        if (is_string($row)) {
            $row = explode(',', $row);
            $row = array_map('trim', $row);
        }
        return fputcsv($this->_handle, $row, $this->_delimiter, $this->_enclosure);
    }

    /**
     *
     * @param array $array
     */
    public function writeFromArray(array $array)
    {
        foreach ($array as $key => $value) {
            $this->writeRow($value);
        }
    }


}
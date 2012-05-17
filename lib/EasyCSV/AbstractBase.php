<?php

namespace EasyCSV;

/**
 *
 * @author chente
 *
 */
abstract class AbstractBase
{

    protected $_handle;
    protected $_delimiter = ',';
    protected $_enclosure = '"';
    protected $_path;
    protected $_mode;

    /**
     *
     * @param unknown_type $path
     * @param unknown_type $mode
     */
    public function __construct($path, $mode = 'r+')
    {
        $this->_path = $path;
        $this->_mode = $mode;
        $this->openFile();
    }

    /**
     *
     */
    public function __destruct(){
        $this->closeFile();
    }

    /**
     *
     */
    protected function closeFile(){
        if (is_resource($this->_handle)) {
            fclose($this->_handle);
        }
    }

    /**
     *
     */
    protected function openFile(){
        $this->_handle = fopen($this->_path, $this->_mode);
    }

}

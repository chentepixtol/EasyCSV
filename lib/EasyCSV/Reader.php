<?php

namespace EasyCSV;

/**
 *
 * @author chente
 */
class Reader extends AbstractBase implements \Iterator
{

    private $_headers;
    private $_line;
    private $currentRow;
    private $isValid = false;
    private $firsRow;

    /**
     *
     * @param unknown_type $path
     * @param unknown_type $mode
     */
    public function __construct($path, $mode = 'r+')
    {
        parent::__construct($path, $mode);
        $this->initializate();
    }

    /**
     *
     */
    private function initializate(){
        $this->currentRow = null;
        $this->_headers = $this->fgetcsv();
        $this->isValid = $this->_headers !== false;
        if( is_array($this->_headers) ){
            $this->_headers = array_map('strtolower', $this->_headers);
        }
        $this->_line = 1;
    }

    /**
     * @return array
     */
    protected function fgetcsv(){
        return fgetcsv($this->_handle, 1000, $this->_delimiter, $this->_enclosure);
    }

    /**
     *
     * @return array
     */
    public function getRow()
    {
        if( ($row = $this->fgetcsv()) !== false ) {
            $this->isValid = true;
            $this->_line++;
            $this->currentRow = $this->_headers ? array_combine($this->_headers, $row) : $row;
            return $this->currentRow;
        } else {
            $this->isValid = false;
            return $this->isValid;
        }
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::current()
     */
    public function current(){
        return null == $this->currentRow ? $this->getRow() : $this->currentRow;
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::key()
     */
    public function key(){
        return $this->_line;
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::next()
     */
    public function next(){
        $this->getRow();
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::rewind()
     */
    public function rewind(){
        $this->closeFile();
        $this->openFile();
        $this->initializate();
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::valid()
     */
    public function valid(){
        return $this->isValid;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        $data = array();
        while ($row = $this->getRow()) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     *
     * @return number
     */
    public function getLineNumber(){
        return $this->_line;
    }

    /**
     * @return array
     */
    protected function getHeaders(){
        return $this->_headers;
    }
}
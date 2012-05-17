<?php

namespace EasyCSV;

class Reader extends AbstractBase implements \Iterator
{
    private $_headers;
    private $_line;

    private $currentRow;
    private $isValid = false;
    private $firsRow;

    public function __construct($path, $mode = 'r+')
    {
        parent::__construct($path, $mode);
        $this->initializate();
    }

    private function initializate(){
        $this->currentRow = null;
        $this->_headers = $this->fgetcsv();
        $this->isValid = $this->_headers !== false;
        $this->_line = 1;
    }

    protected function getHeaders(){
        return $this->_headers;
    }

    protected function fgetcsv(){
        return fgetcsv($this->_handle, 1000, $this->_delimiter, $this->_enclosure);
    }

    public function getRow()
    {
        if( ($row = $this->fgetcsv()) !== false) {
            $this->isValid = true;
            $this->_line++;
            $this->currentRow = $this->_headers ? array_combine($this->_headers, $row) : $row;
            return $this->currentRow;
        } else {
            $this->isValid = false;
            return $this->isValid;
        }
    }

    public function current(){
        return null == $this->currentRow ? $this->getRow() : $this->currentRow;
    }

    public function key(){
        return $this->_line;
    }

    public function next(){
        $this->getRow();
    }

    public function rewind(){
        $this->closeFile();
        $this->openFile();
        $this->initializate();
    }

    public function valid(){
        return $this->isValid;
    }

    public function getAll()
    {
        $data = array();
        while ($row = $this->getRow()) {
            $data[] = $row;
        }
        return $data;
    }

    public function getLineNumber()
    {
        return $this->_line;
    }
}
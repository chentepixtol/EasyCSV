<?php

namespace EasyCSV\Tests;


class ReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testOneAtAtime()
    {
        $reader  = $this->getReader();
        while($row = $reader->getRow()) {
            $this->assertTrue(is_array($row));
            $this->assertEquals(4, count($row));
        }
    }

    public function testGetAll()
    {
        $this->assertEquals(5, count($this->getReader()->getAll()));
    }

    public function testOthet(){
        $reader  = $this->getReader();
        print_r($reader->getAll());die();
    }

    /**
     * @test
     */
    public function iterator()
    {
        $iterator = $this->getReader();
        $rowsWhile = array();
        $line = 1;
        while( $iterator->valid() ) {
            $row = $iterator->current();
            $line++;
            $rowsWhile[] = $row;

            $this->assertEquals(array("column1", "column2", "column3", 'column4'), array_keys($row));
            $this->assertEquals($line, $iterator->key());

            $iterator->next();
        }
        $iterator->rewind();

        $this->assertEquals(1, $iterator->getLineNumber());
        $rowsFor = array();
        $line = 1;
        foreach($iterator as $key => $row){
            $line++;
            $rowsFor[] = $row;
            $this->assertEquals(array("column1", "column2", "column3", 'column4'), array_keys($row));
            $this->assertEquals($line, $key);
        }

        $this->assertEquals($rowsWhile, $rowsFor);
    }

    /**
     * @return \EasyCSV\Reader
     */
    private function getReader(){
        return new \EasyCSV\Reader(__DIR__ . '/mocks/read.csv');
    }
}
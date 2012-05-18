<?php

namespace EasyCSV\Tests;

use EasyCSV\ValidationException;

use EasyCSV\Checker;

use EasyCSV\Reader;

class CheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \EasyCSV\Exception
     */
    public function inexistenteFile(){
        $reader = new Reader('notexists.csv');
    }

    /**
     * @test
     * @expectedException \EasyCSV\ValidationException
     */
    public function withoutHeaders(){

        $reader = new Reader(dirname(__FILE__).'/mocks/other_layout.csv');
        $checker = $this->getChecker();
        $checker->check($reader);
    }

    /**
     * @test
     * @expectedException \EasyCSV\ValidationException
     */
    public function nullables(){

        $reader = new Reader(dirname(__FILE__).'/mocks/nullables.csv');
        $checker = $this->getChecker();
        $checker->check($reader);
    }

    /**
     * @test
     */
    public function invalidValues(){

        $reader = new Reader(dirname(__FILE__).'/mocks/invalidValues.csv');
        $checker = $this->getChecker();
        try {
            $checker->check($reader);
            $this->fail("Debio de generar una exception");
        }catch (ValidationException $e){
            $this->assertEquals(20, count($e->getErrors()));
        }
    }

    /**
     * @return \EasyCSV\Checker
     */
    private function getChecker(){
        $checker = new Checker(array('name', 'email', 'phone', 'genre'));
        $checker->addRule('name', '/^[a-zA-Z\s]{3,}$/', "El nombre introducido '%value%' es incorrecto");
        $checker->addRule('email', '/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*+[a-z]{2}/is', "El email introducido '%value%' es incorrecto");
        $checker->addRule('phone', '/^[0-9]{8}$/', "El telefono introducido '%value%' es incorrecto");
        $checker->addRule('genre', '/^(F|M)$/', "El genero introducido '%value%' es incorrecto solamente F o M");
        return $checker;
    }

}
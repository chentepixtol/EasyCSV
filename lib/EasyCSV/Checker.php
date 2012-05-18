<?php

namespace EasyCSV;

/**
 * Clase que checa la integridad de un archivo CSV
 *
 */
class Checker
{

    /**
     * Reglas para el archivo a revisar
     *
     * @var mixed
     */
    private $rules = array();

    /**
     * Errores encontrados en el archivo
     *
     * @var mixed
     */
    private $errors = array();

    /**
     * Indices que debe tener el archivo
     *
     * @var mixed
     */
    private $index = array();

    /**
     * Deterina si el documento debe tener el mismo numero de columnas en todas sus filas (tomando como base el numero de filas de la primera linea)
     *
     * @var boolean
     */
    private $checkRowNumbers = false;

    /**
     * El numero de columnas que deben tener TODAS las filas (si es cero se anula)
     *
     * @var int
     */
    private $fixedColumnNumber = 0;

    /**
     *
     * @var Reader
     */
    private $reader;

    /**
     * Constructor de la clase
     *
     * @param int $columnNumber
     */
    public function __construct($columnNumber = 0)
    {
        $this->fixedColumnNumber = $columnNumber;
    }

    /**
     * Guarda las reglas del archivo que se va a revisar
     * Ejemplo:
     * <code>
     * <?php
     * $CsvChecker->setRules(array('nombre'=>'^[a-zA-Z ]{5,20}$'));
     * ?>
     * </code>
     * @param mixed $rules
     * @example
     *
     */
    public function setRules($rules)
    {
        if(! is_array($rules))
            throw new \Exception(' Las reglas del documento deben ser enviadas como un arreglo asociativo @ ' . __LINE__);
        $this->rules = $rules;

    }

    /**
     * Guarda una regla para el archivo
     *
     * @param string $field
     * @param string $regexp
     * @param boolean $CanBeNull
     * @param string $invalidMessage
     * @return CsvChecker $this
     */
    public function setRule($field, $regexp, $CanBeNull, $invalidMessage)
    {
        $this->rules[$field] = array(
            'regexp' => $regexp,
            'null' => $CanBeNull,
            'invalid' => $invalidMessage);
        return $this;
    }


    public function addRequired($field)
    {
        $this->rules[$field] = array(
            'required' => true,
        );
        return $this;
    }

    /**
     * Genera una excepcion a algunas de las reglas establecidas para un valor dado
     *
     * @param string $field
     * @param string $regexp
     * @param array $ignoreFields
     */
    public function setConditionalIgnores($field, $regexp, $ignoreFields)
    {
        foreach ($ignoreFields as $ignoreField)
        {
            if(isset($this->rules[$ignoreField]))
            {
                $this->rules[$ignoreField]['ignore'][] = array('field'=>$field,'regexp' => $regexp);
            }
        }
    }

    /**
     * Guarda los indices que se buscaran en el archivo
     *
     * @param mixed $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }

    /**
     * Realiza la revision del archivo
     *
     * @param string $fileName
     */
    private function initialize(Reader $reader)
    {
        $this->checkCsvFile($reader->getFilename());
        if( count($this->rules) === 0 ){
            throw new \Exception(' No se puede revistar el documento sin antes haber definido reglas @ ' . __LINE__);
        }

        $index = $reader->getHeaders();
        if( count($this->index) > 0 )
        {
            $faltan = array_diff($this->index, $index);
            if(count($faltan)){
                $this->setError('Se detectaron columnas faltantes en el archivo, se necesitan <strong>' . implode(', ', $this->index) . '</strong><br/><br/> y se encontraron <strong>' . implode(', ', $index) . '</strong><br/><br/> Faltando <strong>' . implode(', ', $faltan) . '</strong>');
            }
        }
        $reader->rewind();
        while ( $reader->valid() ){
            $this->applyRules($reader->read(), $reader->getLineNumber());
        }
    }

    /**
     *
     * Verifica la integridad del archivo
     * @param unknown_type $fileName
     * @throws FileException
     */
    public function check(Reader $reader)
    {
        $this->initialize($reader);
        $e = $this->getErrors();
        if ( $e != false )
        {
            throw new \Exception('Se encontraron errores en el archivo');
        }

        $reader->rewind();
    }

    /**
     * Applica las reglas establecidas en una linea del archivo
     *
     * @param mixed $row
     * @param int $lineNumber
     */
    private function applyRules($row, $lineNumber)
    {
        foreach($this->rules as $index => $rule)
        {
            if(isset($row[$index]))
            {
                if(isset($rule['regexp']))
                {
                    if( !preg_match($rule['regexp'], $row[$index]))
                    {
                        if($row[$index] == '' && $rule['null'] == true)
                        {
                            continue;
                        }
                        if( isset($rule['ignore']) )
                        {
                            foreach ($rule['ignore'] as $ignoreRule){
                                if( preg_match( $ignoreRule['regexp'] , $row[$ignoreRule['field']] ) ){
                                  continue 2;
                                }
                            }
                        }

                        $errorLine = $lineNumber . ' @ ';
                        if($row[$index] == ''){
                            $row[$index] = '<strong>vacío</strong>';
                        }

                        $errorLine .= str_replace('%field%', '<strong>' . $index . '</strong>', str_replace('%value%', $row[$index], $rule['invalid']));
                        $this->setError($errorLine);
                    }
                } else if(isset($rule['required']) && $rule['required'] == true)
                {
                    if( !isset($row[$index]) )
                    {
                        $errorLine = $lineNumber . ' @ El campo es requerido '. $index;
                        $this->setError($errorLine);
                    }
                }
            } else
            {
                $errorLine = $lineNumber . ' @ El campo <strong>' . $index . '</strong> no ha sido definido ';
                $this->setError($errorLine);
            }
        }
    }

    /**
     * Check if the File is a real CSV File
     *
     * @param string $fileName
     * @return boolean
     */
    private function checkCsvFile($filepath)
    {
        if( $this->getFileExtension($filepath) != 'csv' ){
            throw new \Exception('El archivo que selecciono no es un fichero v&aacute;lido');
        }

        if( filesize($filepath) == 0 ){
            throw new \Exception('El archivo que selecciono no es un fichero v&aacute;lido o parece estar vacio');
        }

        $content = file_get_contents($filepath);
        if( !preg_match("/(,(.[^,]*)){1,10}/", $content) ){
            throw new \Exception('El archivo que selecciono no es un fichero v&aacute;lido o está da&ntilde;ado');
        }
        $content = null;
    }

    /**
     * Obtiene la extensión del archivo seleccionado
     *
     * @param string $filepath
     * @return string
     */
    private function getFileExtension($filepath)
    {
        if( $filepath != "" )
        {
            $info = pathinfo($filepath);
            return strtolower($info["extension"]);
        }
        return '';
    }

    /**
     * Guarda un eror en el registro de errores
     * @param string $error
     */
    public function setError($error)
    {
        array_push($this->errors, $error);
    }

    /**
     * @return boolean
     */
    public function hasErrors()
    {
        if(count($this->errors))
            return true;
        else
            return false;
    }

    /**
     * Regresa el arreglo de errores si existen, sino regresa falso
     *
     * @return mixed|boolean
     */
    public function getErrors()
    {
        if(count($this->errors))
            return $this->errors;
        else
            return false;
    }

    /**
     * Guarda un boleano para determinar si se debe revisar el numero de columnas en las lineas del archivo
     *
     * @param boolean $checkRowNumbers
     */
    public function setCheckRowNumbers($checkRowNumbers){
        $this->checkRowNumbers = $checkRowNumbers;
    }

}

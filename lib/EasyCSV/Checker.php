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
    protected $rules = array();

    /**
     * Errores encontrados en el archivo
     *
     * @var mixed
     */
    protected $errors = array();

    /**
     * Indices que debe tener el archivo
     *
     * @var mixed
     */
    protected $index = array();

    /**
     *
     * @var Reader
     */
    protected $reader;

    /**
     * Constructor de la clase
     *
     * @param array $headers
     */
    public function __construct(array $headers){
        $this->setIndex($headers);
    }

    /**
     * Guarda una regla para el archivo
     *
     * @param string $field
     * @param string $regexp
     * @param boolean $CanBeNull
     * @param string $invalidMessage
     * @param string $requiredMessage
     * @return Checker
     */
    public function addRule($field, $regexp, $invalidMessage, $required = true, $requiredMessage = "The field %field% is required")
    {
        $this->rules[$field] = array(
            'regexp'   => $regexp,
            'required' => $required,
            'message'  => $invalidMessage,
            'requiredMessage'  => $requiredMessage,
        );
        return $this;
    }

    /**
     *
     * @param unknown_type $field
     * @param unknown_type $invalidMessage
     * @return Checker
     */
    public function addRequired($field, $requiredMessage = "The field %field% is required"){
        return $this->addRule($field, '/^(.){1,}$/', "", true, $requiredMessage);
    }

    /**
     * Guarda los indices que se buscaran en el archivo
     *
     * @param mixed $index
     */
    protected function setIndex($index){
        $this->index = $index;
    }

    /**
     * Realiza la revision del archivo
     *
     * @param string $fileName
     */
    protected function initialize(Reader $reader)
    {
        $this->checkCsvFile($reader->getFilename());
        if( count($this->rules) === 0 ){
            throw new Exception(' No se puede revistar el documento sin antes haber definido reglas @ ' . __LINE__);
        }

        $index = $reader->getHeaders();
        if( count($this->index) > 0 )
        {
            $faltan = array_diff($this->index, $index);
            if( count($faltan) ){
                $this->addError('Se detectaron columnas faltantes en el archivo, se necesitan ' . implode(', ', $this->index) . ' y se encontraron ' . implode(', ', $index) . ' Faltando ' . implode(', ', $faltan) . '');
            }
        }

        $reader->rewind();
        while ( $reader->valid() ){
            $this->applyRules($reader->current(), $reader->getLineNumber());
            $reader->next();
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
        $reader->rewind();

        if ( $this->hasErrors() ){
            throw new ValidationException("Se encontraron errores en el archivo", $this->getErrors());
        }
    }

    /**
     * Applica las reglas establecidas en una linea del archivo
     *
     * @param mixed $row
     * @param int $lineNumber
     */
    protected function applyRules($row, $lineNumber)
    {
        foreach( $this->rules as $field => $rule )
        {

            $isEmpty = empty($row[$field]);
            $isFilled = !$isEmpty;

            if( $rule['required'] && $isEmpty ){
                $this->addError($lineNumber . ' @ '. str_replace('%field%', '' . $field . '', $rule['requiredMessage']));
                continue;
            }

            if( $isFilled && !preg_match($rule['regexp'], $row[$field]) ){
                $errorLine = $lineNumber . ' @ ' . str_replace('%field%', '' . $field . '', str_replace('%value%', $row[$field], $rule['message']));
                $this->addError($errorLine);
            }
        }
    }

    /**
     * Check if the File is a real CSV File
     *
     * @param string $fileName
     * @return boolean
     */
    protected function checkCsvFile($filepath)
    {
        if( $this->getFileExtension($filepath) != 'csv' ){
            throw new Exception('El archivo que selecciono no es un fichero v&aacute;lido');
        }

        if( filesize($filepath) == 0 ){
            throw new Exception('El archivo que selecciono no es un fichero v&aacute;lido o parece estar vacio');
        }

        $content = file_get_contents($filepath);
        if( !preg_match("/(,(.[^,]*)){1,10}/", $content) ){
            throw new Exception('El archivo que selecciono no es un fichero v&aacute;lido o está da&ntilde;ado');
        }
        $content = null;
    }

    /**
     * Obtiene la extensión del archivo seleccionado
     *
     * @param string $filepath
     * @return string
     */
    protected function getFileExtension($filepath)
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
    protected function addError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * @return boolean
     */
    public function hasErrors(){
        return count($this->errors) > 0;
    }

    /**
     * Regresa el arreglo de errores si existen, sino regresa falso
     *
     * @return mixed
     */
    public function getErrors(){
        return $this->errors;
    }

}

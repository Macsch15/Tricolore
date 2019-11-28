<?php
namespace Tricolore\Exception;

class ErrorException extends \Exception
{
    /**
     * File
     * 
     * @var string
     */
    private $_file;

    /**
     * Line
     * 
     * @var
     */
    private $_line;

    /**
     * Construct
     * 
     * @param string $message
     * @param int $code
     * @param string $file
     * @param int $line
     * @return void
     */
    public function __construct($message, $code, $file, $line)
    {
        $message = sprintf('%s in %s on line %s (code %d)', $message, $file, $line, $code);

        parent::__construct($message, $code);

        $this->_file = $file;
        $this->_line = $line;
    }

    /**
     * Error file
     * 
     * @return string
     */
    public function getErrorFile()
    {
        return $this->_file;
    }

    /**
     * Error line
     *  
     * @return int
     */
    public function getErrorLine()
    {
        return $this->_line;
    }
}

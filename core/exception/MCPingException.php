<?php
/**
 * @author WarewolfCZ
 */
require_once('MCException.php');

class MCPingException extends MCException {
    public function errorMessage() {
        //error message
        $errorMsg = 'Error on line '.$this->getLine().' in '.$this->getFile()
        .': <b>'.$this->getMessage().'</b>';
        return $errorMsg;
    }
}
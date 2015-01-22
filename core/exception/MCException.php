<?php
/**
 * @author WarewolfCZ
 */
class MCException extends Exception {
    public function errorMessage() {
        //error message
        $errorMsg = 'Error on line '.$this->getLine().' in '.$this->getFile()
        .': <b>'.$this->getMessage().'</b>';
        return $errorMsg;
    }
}
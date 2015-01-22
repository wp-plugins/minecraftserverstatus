<?php

/**
 * Status response from server
 *
 * @author WarewolfCZ
 */
class MCStatus {
    private $latency;
    private $values;
    
    /**
     * 
     * @param type $value
     */
    public function setLatency($value) {
        $this->latency = $value;
    }
    
    /**
     * 
     * @param type $json
     * @throws MCException
     */
    public function decodeJson($json) {
        $this->values = json_decode($json, true); 
        if ($this->values == NULL || $this->values == FALSE) {
            throw new MCException("Cannot decode status JSON. Error code: " . 
            json_last_error() . ": " . $this->getJsonLastErrMsg(json_last_error()));
        }
    }
    
    /**
     * 
     * Get error message based on json_last_error() error code
     * @param int $errCode
     */
    private function getJsonLastErrMsg($errCode) {
        $result = NULL;
        switch ($errCode) {
            case JSON_ERROR_NONE:
                $result = ' - No errors';
                break;
            case JSON_ERROR_DEPTH:
                $result = ' - Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $result = ' - Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $result = ' - Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                $result = ' - Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                $result = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                $result = ' - Unknown error';
            break;
        }
        return $result;
    } 
    
    /**
     * 
     * @return float
     */
    public function getLatency() {
        return $this->latency;
    }
    
    /**
     * 
     * @return int
     */
    public function getOnlinePlayers() {
        $result = -1;
        if ($this->values != NULL) {
            $result = $this->values["players"]["online"];
        }
        return $result;
    }
    
    /**
     * 
     * @return int
     */
    public function getMaxPlayers() {
        $result = -1;
        if ($this->values != NULL) {
            $result = $this->values["players"]["max"];
        }
        return $result;
    }
    
    /**
     * 
     * @return string
     */
    public function getVersion() {
        $result = NULL;
        if ($this->values != NULL) {
            $result = $this->values["version"]["name"];
        }
        return $result;
    }
    
        
    /**
     * 
     * @return string
     */
    public function getDescription() {
        $result = NULL;
        if ($this->values != NULL) {
            $result = $this->values["description"];
        }
        return $result;
    }
    
    /**
     * Get protocol version number
     * @return string
     */
    public function getProtocol() {
        $result = NULL;
        if ($this->values != NULL) {
            $result = $this->values["version"]["protocol"];
        }
        return $result;
    }
    
    //TODO: get favicon
}

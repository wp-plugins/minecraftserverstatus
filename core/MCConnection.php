<?php

/**
 * @author WarewolfCZ
 */
require_once("MCPacket.php");
require_once("exception/MCException.php");

class MCConnection {

    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function writePacket(MCPacket $packet) {
        $data = $packet->flush();
        $packet->writeVarInt(strlen($data));
        $length = $packet->flush();
        // send data length first
        fwrite($this->conn, $length);
        fwrite($this->conn, $data);
    }

    public function readPacket() {
        $result = NULL;
        // first value is always length
        $length = $this->freadVarInt();
        $buffer = NULL;
        $readBytes = 0;
        while (!feof($this->conn) && $readBytes < $length) {
            $chunk = fread($this->conn, $length);
            $readBytes += strlen($chunk);
            $buffer .= $chunk;
        }
        if ($buffer != FALSE) {
            $result = new MCPacket($buffer);
        }
        return $result;
    }

    private function freadVarInt() {
        $result = 0;
        for ($i = 0; $i < 5; $i++) {
            $byte = fread($this->conn, 1);
            if ($byte == FALSE) {
                break;
            }
            // bindec is expecting string, not binary string => not usable
            $part = hexdec(bin2hex($byte));
            // add part to result (shift by i * 7)
            $result |= ($part & 0x7F) << 7 * $i;
            if (($part & 0x80) == 0) { // 8th bit is set to zero => last octet of VarInt
                return $result;
            }
        }
        throw new MCException("Server sent invalid length");
    }

}

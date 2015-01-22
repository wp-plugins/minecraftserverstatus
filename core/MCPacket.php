<?php

/**
 * @author WarewolfCZ
 */
require_once('exception/MCException.php');

class MCPacket {

    private $data;
    private $position;

    public function __construct($buffer = NULL) {
        $this->data = $buffer;
        $this->position = 0;
    }

    /**
     * Store VarInt value to data buffer
     */
    public function writeVarInt($value) {
        $remaining = (int) $value;
        for ($i = 0; $i < 5; $i++) {
            if (($remaining & ~0x7F) == 0) { // 8th bit is 0
                $this->data .= pack("C", $remaining); // pack as unsigned char
                return;
            } else {
                // clear everything but 7 least significant bits and set 8th bit to 1
                $this->data .= pack("C", $remaining & 0x7F | 0x80); // pack as unsigned char
            }
            // shift right
            $remaining = $remaining >> 7;
        }
        throw new MCException("The value " . $value . " is too big to store in VarInt");
    }

    /**
     * Store long value to data buffer
     */
    public function writeLong($value) {
        // PHP 5.4 doesn't support packing of 64bit numbers
        // so we must pack it as 2x32bit
        $highMap = 0xffffffff00000000;
        $lowMap = 0x00000000ffffffff;
        $higher = ($value & $highMap) >> 32;
        $lower = $value & $lowMap;
        $this->data .= pack('NN', $higher, $lower);
    }

    /**
     * Store short value to data buffer
     */
    public function writeShort($value) {
        $this->data .= pack("n", $value);
    }

    /**
     * Store string to data buffer
     */
    public function writeUtf($value) {
        if ($value != NULL) {
            // add string size at the beginning
            $this->writeVarInt(strlen($value));
            $this->data .= pack('a*', $value);
        }
    }

    /**
     * Parse next VarInt value and return it, buffer position is incremented
     */
    public function readVarInt() {
        $result = 0;
        if ($this->data != NULL) {
            for ($i = 0; $i < 5; $i++) {
                $index = $this->position + $i;
                // bindec is expecting string, not binary string => not usable
                $part = hexdec(bin2hex(substr($this->data, $index, 1)));
                //printf("part: %d <br/>", $part);
                // add part to result (shift by i * 7)
                $result |= ($part & 0x7F) << 7 * $i;
                if (($part & 0x80) == 0) { // 8th bit is set to zero => last octet of VarInt
                    $this->position += $i + 1;
                    return $result;
                }
            }
            throw new MCException("Server sent a varint that was too big!");
        }
    }

    /**
     * Parse next short value and return it + increment buffer position
     */
    public function readShort() {
        $result = 0;
        if ($this->data != NULL && strlen($this->data) >= $this->position + 2) {
            $arr = unpack("n", substr($this->data, $this->position, 2));
            if (count($arr) > 0) {
                $result = $arr[1];
                $this->position += 2;
            }
        }
        return $result;
    }

    /**
     * Parse next long value and return it, buffer position is incremented
     */
    public function readLong() {
        $result = NULL;
        if ($this->data != NULL && strlen($this->data) >= $this->position + 8) {
            // PHP 5.4 doesn't support unpacking 64bit numbers => we will unpack it as two 32bit
            $arr = unpack('N2', substr($this->data, $this->position, 8));
            if (count($arr) > 1) {
                $result = $arr[1] << 32 | $arr[2];
                $this->position += 8;
            }
        }
        return $result;
    }

    /**
     * Parse next string value and return it, buffer position is incremented
     */
    public function readUtf() {
        $result = NULL;
        if ($this->data != NULL) {
            $length = $this->readVarInt();
            $bindata = substr($this->data, $this->position, $length);
            $arr = unpack("a*", $bindata);
            if (count($arr) > 0) {
                $this->position += $length;
                $result = $arr[1];
            }
        }
        return $result;
    }

    /**
     * Clear data value and reset position counter
     */
    public function clearData() {
        $this->data = NULL;
        $this->position = 0;
    }

    /**
     * Reset position counter to zero
     */
    public function rewind() {
        $this->position = 0;
    }

    /**
     * Return content of data buffer and clear it
     */
    public function flush() {
        $result = $this->data;
        $this->clearData();
        return $result;
    }

        /**
     * Return content of data buffer
     */
    public function getBuffer() {
        $result = $this->data;
        return $result;
    }
}

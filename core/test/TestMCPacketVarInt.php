<?php
/**
 * @author WarewolfCZ
 */
require_once("MCPacket.php");
$result = true;

// test clear
echo "Test varint clear<br />";
$packet = new MCPacket();
$packet->writeVarInt(125);
$packet->clearData();
$value = $packet->readVarInt();
$result = $result && assert($value == NULL);
$result = $result && assert(strlen($packet->getBuffer()) == 0);

// test VarInt
echo "Test VarInt<br />";
$packet = new MCPacket();
$packet->writeVarInt(300);
$value = $packet->readVarInt();
$result = $result && assert(bin2hex($packet->getBuffer()) == "ac02"); // 1010 1100 0000 0010
$result = $result && assert($value == 300);

// test two VarInt values
echo "Test two VarInt values<br />";
$packet = new MCPacket();
$packet->writeVarInt(300);
$packet->writeVarInt(5980);
$value1 = $packet->readVarInt();
$value2 = $packet->readVarInt();
$result = $result && assert($value1 == 300);
$result = $result && assert($value2 == 5980);

if ($result) {
    echo "MCPacket varint tests OK<br/>\n";
} else {
    echo "MCPacket varint tests failed<br/>\n";
}
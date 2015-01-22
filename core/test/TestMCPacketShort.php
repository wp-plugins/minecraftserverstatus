<?php
/**
 * @author WarewolfCZ
 */
require_once("MCPacket.php");
$result = true;

// test clear
echo "Test clear<br />";
$packet = new MCPacket();
$packet->writeShort(52);
$packet->clearData();
$value = $packet->readShort();
$result = $result && assert($value == NULL);
$result = $result && assert(strlen($packet->getBuffer()) == 0);

// test short
echo "Test short<br />";
$packet = new MCPacket();
$packet->writeShort(54);
$value = $packet->readShort();
$result = $result && assert(bin2hex($packet->getBuffer()) == "0036");
$result = $result && assert($value == 54);

// test2 short
echo "Test2 short<br />";
$packet = new MCPacket();
$packet->writeShort(25565);
$value = $packet->readShort();
echo "getBuffer(): " . bin2hex($packet->getBuffer()) . "<br/>";
$result = $result && assert(bin2hex($packet->getBuffer()) == "63dd"); // 1010 1100 0000 0010

// test two short values
echo "Test 2x short<br />";
$packet = new MCPacket();
$packet->writeShort(54);
$packet->writeShort(32);
$value1 = $packet->readShort();
$value2 = $packet->readShort();
$result = $result && assert($value1 == 54);
$result = $result && assert($value2 == 32);
$result = $result && assert(bin2hex($packet->getBuffer()) == "00360020");



if ($result) {
    echo "MCPacket short tests OK<br/>\n";
} else {
    echo "MCPacket short tests failed<br/>\n";
}
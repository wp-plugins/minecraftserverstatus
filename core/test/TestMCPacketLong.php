<?php
/**
 * @author WarewolfCZ
 */
require_once("MCPacket.php");
$result = true;

// test clear
echo "Test clear<br />";
$packet = new MCPacket();
$packet->writeLong(125);
$packet->clearData();
$value = $packet->readLong();
$result = $result && assert($value == NULL);
$result = $result && assert(strlen($packet->getBuffer()) == 0);

// test single long
echo "Test 1x long<br />";
$packet = new MCPacket();
$packet->writeLong(125);
$value = $packet->readLong();
$result = $result && assert($value == 125);

// test 2x long
$packet = new MCPacket();
echo "Test 2x long<br />";
$packet->writeLong(9864654654125);
$packet->writeLong(6622644);
$value1 = $packet->readLong();
$value2 = $packet->readLong();
$result = $result && assert($value1 == 9864654654125);
$result = $result && assert($value2 == 6622644);


echo strtoupper(bin2hex($packet->getBuffer())) . "<br />\n";
echo strlen($packet->getBuffer()) . "<br />\n";

if ($result) {
    echo "MCPacket long tests OK<br/>\n";
} else {
    echo "MCPacket long tests failed<br/>\n";
}
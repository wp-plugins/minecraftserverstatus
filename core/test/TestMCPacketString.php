<?php
/**
 * @author WarewolfCZ
 */
require_once("MCPacket.php");
$result = true;

// test clear
echo "Test clear<br />";
$packet = new MCPacket();
$packet->writeUtf("FOO");
$packet->clearData();
$value = $packet->readUtf();
$result = $result && assert($value == NULL);
$result = $result && assert(strlen($packet->getBuffer()) == 0);

// test string
echo "Test string<br />";
$packet = new MCPacket();
$packet->writeUtf("FOO");
$value = $packet->readUtf();
$result = $result && assert(bin2hex($packet->getBuffer()) == "03464f4f");
$result = $result && assert($value == "FOO");

// test two strings
echo "Test two strings<br />";
$packet = new MCPacket();
$packet->writeUtf("FOO");
$packet->writeUtf("BAR");
$value1 = $packet->readUtf();
$value2 = $packet->readUtf();
$result = $result && assert(bin2hex($packet->getBuffer()) == "03464f4f03424152");
$result = $result && assert($value1 == "FOO");
$result = $result && assert($value2 == "BAR");

//$value = unpack('H*', "Stack");
//echo base_convert($value[1], 16, 2);

if ($result) {
    echo "MCPacket string tests OK<br/>\n";
} else {
    echo "MCPacket string tests failed<br/>\n";
}
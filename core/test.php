<?php
/**
 * @author WarewolfCZ
 */
error_reporting(E_ALL);
ini_set("display_errors", 1);

require("test/TestMCPacketVarInt.php");
require("test/TestMCPacketString.php");
require("test/TestMCPacketShort.php");
require("test/TestMCPacketLong.php");

require_once(__DIR__.'/MCServer.php');



try {
   $server = new MCServer("77.93.202.250", 25565);
   printf("Ping latency: %1.3f ms<br />\n", $server->ping());
   $status = $server->status();
   printf("Slots: %d<br />\n", $status->getMaxPlayers());
   printf("Version: %s<br />\n", $status->getVersion());
   printf("Description: %s<br />\n", $status->getDescription());
   printf("Protocol: %d<br />\n", $status->getProtocol());
} catch (MCPingException $e) {
    echo '<br/>'. $e->errorMessage() . '<br/>';
} catch (MCConnException $e) {
    echo '<br/>'. $e->errorMessage() . '<br/>';
}

echo '<br/>';

try {   
   $server = new MCServer("mc.hypixel.net", 25565, 5);
   $status = $server->status();
   printf("Ping latency: %1.3f ms<br />\n", $status->getLatency());
   printf("Slots: %d<br />\n", $status->getMaxPlayers());
   printf("Version: %s<br />\n", $status->getVersion());
   printf("Description: %s<br />\n", $status->getDescription());
   printf("Protocol: %d<br />\n", $status->getProtocol());
   
} catch (MCPingException $e) {
    echo '<br/>'. $e->errorMessage() . '<br/>';
} catch (MCConnException $e) {
    echo '<br/>'. $e->errorMessage() . '<br/>';
}


echo '<br/>';

try {   
   $server = new MCServer("93.91.250.117", 27246, 4);
   $status = $server->status();
   printf("Ping latency: %1.3f ms<br />\n", $status->getLatency());
   printf("Slots: %d<br />\n", $status->getMaxPlayers());
   printf("Version: %s<br />\n", $status->getVersion());
   printf("Description: %s<br />\n", $status->getDescription());
   printf("Protocol: %d<br />\n", $status->getProtocol());
   
} catch (MCPingException $e) {
    echo '<br/>'. $e->errorMessage() . '<br/>';
} catch (MCConnException $e) {
    echo '<br/>'. $e->errorMessage() . '<br/>';
}
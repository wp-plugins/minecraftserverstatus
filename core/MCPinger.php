<?php

/**
 * @author WarewolfCZ
 */
require_once('MCPacket.php');
require_once('MCStatus.php');

class MCPinger {

    private $conn;
    private $host;
    private $port;
    private $version;
    private $pingToken;

    public function __construct($connection, $host, $port = 0, $version = 47, $pingToken = NULL) {
        $this->conn = $connection;
        $this->host = $host;
        $this->port = $port;
        $this->version = $version;
        if ($pingToken == NULL) {
            $pingToken = rand(0, (1 << 63) - 1);
        }
        $this->pingToken = $pingToken;
    }

    /**
     * Initialize communication
     */
    public function handshake() {
        $packet = new MCPacket();
        $packet->writeVarInt(0);
        $packet->writeVarInt($this->version);
        $packet->writeUtf($this->host);
        $packet->writeShort($this->port);
        $packet->writeVarInt(1);  // Intention to query status
        $this->conn->writePacket($packet);
    }

    /**
     * Ping server and return latency [ms]
     * @return float
     * @throws MCPingException
     */
    public function ping() {
        // create and send ping request
        $packet = new MCPacket();
        $packet->writeVarInt(1); // Test ping
        $packet->writeLong($this->pingToken);
        $sent = microtime(true);
        $this->conn->writePacket($packet);

        // receive ping response
        $response = $this->conn->readPacket();
        if ($response != NULL) {
            $received = microtime(true);
            if ($response->readVarInt() != 1) {
                throw new MCPingException("Received invalid ping response packet.");
            }
            $receivedToken = $response->readLong();
            if ($receivedToken != $this->pingToken) {
                throw new MCPingException("Received mangled ping response packet (expected token \"" .
                $this->pingToken . "\", received \"" . $receivedToken . "\")");
            }
            // calculate time between request and response
            $delta = ($received - $sent) * 1000.0;
            return $delta;
        }
    }

    /**
     * Retrieve server version, players and description
     * @return MCStatus
     * @throws MCException
     */
    public function getStatus() {
        $result = new MCStatus();
        $packet = new MCPacket();
        $packet->writeVarInt(0); // Request status
        $this->conn->writePacket($packet);
        
        
        $response = $this->conn->readPacket();
        if ($response != NULL) {
            if ($response->readVarInt() != 0) {
                throw new MCException("Received invalid status response packet.");
            } else {
                $result->decodeJson($response->readUtf());
                $result->setLatency($this->ping());
            }
        }
        return $result;
    }
}

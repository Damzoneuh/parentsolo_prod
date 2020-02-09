<?php


namespace App\Server;


use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface
{
    private $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $conn->send(sprintf('New connection: Hello '));
    }

    public function onClose(ConnectionInterface $closedConnection)
    {
        $this->clients->detach($closedConnection);
        echo sprintf('Connection has disconnected\n');
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->send('An error has occurred: '.$e->getMessage());
        $conn->close();
    }

    public function onMessage(ConnectionInterface $from, $message)
    {
        $totalClients = count($this->clients) - 1;
        echo vsprintf(
            'Connection #%1$d sending message to other connection%4$s'."\n", [
            $message,
            $totalClients,
            $totalClients === 1 ? '' : 's'
        ]);
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($message);
            }
        }
    }
}
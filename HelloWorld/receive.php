<?php

declare(strict_types=1);

namespace HelloWorld;

use PhpAmqpLib\Connection\AMQPStreamConnection;

require_once __DIR__ . '/../vendor/autoload.php';

// Cria a conexão
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Cria o canal e declara fila (queue) da qual desejamos receber o conteúdo
$channel->queue_declare('nomeFila', false, false, false, false);

echo " [*] Aguardando mensagens. Para sair pressione CTRL + C\n";

$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
};

$channel->basic_consume('nomeFila', '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

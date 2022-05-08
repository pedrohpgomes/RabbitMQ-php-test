<?php

declare(strict_types=1);

namespace tutorial02;

use PhpAmqpLib\Connection\AMQPStreamConnection;

require_once __DIR__ . '/../vendor/autoload.php';

// Cria a conexão e abre um canal de comunicação
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Cria o canal e declara fila (queue) da qual desejamos receber o conteúdo
$channel->queue_declare('novaFila', false, true, false, false);

echo " [*] Aguardando mensagens. Para sair pressione CTRL + C\n";

$callback = function ($msg) {
    echo ' [x] Recebido ', $msg->body, "\n";
    sleep(substr_count($msg->body, '.'));
    echo " [x] Finalizado\n";
    $msg->ack();
};

// É através da opção basic_qos que dizemos ao RabbitMQ para distribuir
// á medida em que um worker terminar o seu trabalho.
// OBS: dessa maneira, se as tarefas levarem muito tempo para concluir,
// a sua fila pode ficar cheia.
// Assim, pode ser necessário adicionar mais workers ou utilizar outra estratégia.
$channel->basic_qos(null, 1, null);
$channel->basic_consume('novaFila', '', false, false, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();

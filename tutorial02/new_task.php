<?php

/**
 * Neste segundo tutorial
 * Foi criado uma fila de trabalho
 * onde o RabbitMQ distribui o trabalho à medida em que um worker termina de processar
 * e fica livre.
 *
 * Desse modo, assim que um worker termina seu trabalho ele avisa à fila e caso houver
 * mais trabalho na fila, ele pega e processa.
 *
 * Neste cenário, a distribuição é feita à medida em que um worker termina um trabalho.
 * Não existe uma ordem pré-definida.
 * Caso existam 02 workers, e o worker-01 receba um trabalho que leva 30 segundos para completar
 * e o worker-02 receba um trabalho que leve 5 segundos, ao terminar o seu trabalho ele avisa à fila,
 * e a fila envia outro trabalho para o worker-02.
 *
 * Esse aviso é feito por meio de acknowledgments (ack).
 *
 * Além disso, s
 */

declare(strict_types=1);

namespace tutorial02;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once __DIR__ . '/../vendor/autoload.php';

// Cria a conexão
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Cria o canal e declara fila (queue) para a qual desejamos enviar o conteúdo
$channel->queue_declare('novaFila', false, true, false, false);

$data = implode('', array_slice($argv, 1));
if (empty($data)) {
    $data = 'Hello World!';
}

/**
 * Ativamos a persistência de um trabalho na fila até que o worker sinalize que terminou com um ack.
 * OBS: pode ser que o RabbitMQ tenha escrito o trabalho em cache e não em disco de fato
 * para salvar um trabalho. Essa persistência é o bastante para aguentar um restart do RabbitMQ e
 * uma fila de trabalhos simples.
 *
 * Caso seja necessário uma garantia forte, é necessário utilizar o recurso "publisher confirms"
 */
$msg = new AMQPMessage(
    $data,
    array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT )
);
$channel->basic_publish($msg, '', 'novaFila');

echo " [x] Enviado $data!\n";

$channel->close();
$connection->close();

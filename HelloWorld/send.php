<?php

/**
 *  Neste primeiro tutorial
 *  foi criado apenas um canal básico de comunicação
 *  onde o sender envia suas msgs e o receiver recebe e processa.
 *
 *  Caso existam 02 receivers por exemplo, o RabbitMQ fará o balanceamento baseado apenas
 *  no número de mensagens, ou seja,
 *  ele enviará a msg 01 para o receive 01,
 *  a msg 02 para o receive 02
 *  a msg 03 para o receive 01
 *  a msg 04 para o receive 02
 *  e assim successivamente, não importando se existe um receiver ocioso.
 *
 *  A distribuição é alternada.
 */

declare(strict_types=1);

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once __DIR__ . '/../vendor/autoload.php';

// Cria a conexão
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Cria o canal e declara fila (queue) para a qual desejamos enviar o conteúdo
$channel->queue_declare('nomeFila', false, false, false, false);

$msg = new AMQPMessage('Minha mensagem');
$channel->basic_publish($msg, '', 'nomeFila');

echo " [x] Mensagem enviada!\n";

$channel->close();
$connection->close();

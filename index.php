<?php
require_once 'vendor/autoload.php';

use Stichoza\GoogleTranslate\GoogleTranslate;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);

$dotenv->load();

$connection = new \Phergie\Irc\Connection();

$connection
    ->setServerHostname('irc.chat.twitch.tv')
    ->setServerPort(6667)
    ->setPassword($_ENV['TOKEN'])
    ->setNickname("#" . $_ENV['CHANNEL'])
    ->setUsername($_ENV['NAME']);

$client = new \Phergie\Irc\Client\React\Client();

$client->on('connect.after.each', function ($connection, $write) {
    global $canal;
    $write->ircJoin($canal);
    $write->ircPrivmsg($canal, 'Gringinho Bot is ON | Gringinho Bot tá ON');
});
 
function traduzir($word, $lang) {
    $params = array( 
        'pt' => array('source' => 'pt', 'target' => 'en'),
        'en' => array('source' => 'en', 'target' => 'pt')
    );
    $tr = new GoogleTranslate();
    $tr->setSource($params[$lang]['source']); 
    $tr->setSource(); 
    $tr->setTarget($params[$lang]['target']);

    return $tr->translate($word);
}

$client->on('irc.received', function ($message, $write, $connection, $logger) {
    global $canal;

    $command = explode(' ', $message['params']['text']);

    if ($message['command'] == 'PRIVMSG') {
        if($command[0] == '!traduzir' || $command[0] == '!translate') {
            $word = str_replace($command[0], ' ', $message['params']['text']);
            $traducao = traduzir($word, $command[0] == '!traduzir' ? 'pt' : 'en');
            $write->ircPrivmsg($canal, $traducao);
        }
    }
});

$client->run($connection);

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
 
function traduzirPt($word) {
    $tr = new GoogleTranslate();
    $tr->setSource('pt'); 
    $tr->setSource(); 
    $tr->setTarget('en');

    return $tr->translate($word);
}

function traduzirEn($word) {
        
    $tr = new GoogleTranslate();
    $tr->setSource('en'); 
    $tr->setSource(); 
    $tr->setTarget('pt');

    return $tr->translate($word);
}

$client->on('irc.received', function ($message, $write, $connection, $logger) {
    global $canal;

    $command = explode(' ', $message['params']['text']);

    if ($message['command'] == 'PRIVMSG') {
        if($command[0] == '!traduzir') {
            $word = str_replace('!traduzir', ' ', $message['params']['text']);
            $traducao = traduzirPt($word);
            $write->ircPrivmsg($canal, $traducao);
        }
        
        if($command[0] == '!translate') {
            $word = str_replace('!translate', ' ', $message['params']['text']);
            $traducao = traduzirEn($word);
            $write->ircPrivmsg($canal, $traducao);
        }
    }

});

$client->run($connection);

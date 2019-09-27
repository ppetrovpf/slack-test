<?php

use Symfony\Component\Yaml\Yaml;

function includeIfExists($file)
{
    if (file_exists($file)) {
        return include $file;
    }
}

if (!$loader = includeIfExists(__DIR__ . '/vendor/autoload.php')) {
    print('Composer install pls.');

    exit(1);
}

$config = Yaml::parseFile('./parameters.yml');

// $client contains all the methods to interact with the API
$client = JoliCode\Slack\ClientFactory::create($config['token']);



//$user = $client->usersInfo(['user' => $config['user_id']])->getUser();
//dump($user);

$client->chatPostMessage([
    'as_user' => true,
    'channel' => $config['user_id'],
    'text' => 'test123',
]);

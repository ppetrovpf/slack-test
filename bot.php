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

// Получить пользователя slack по email:
// https://api.slack.com/methods/users.lookupByEmail
//
// Цель: получить ID пользователя для отправки персональных сообщений и др. действий.
$userByEmail = $client->usersLookupByEmail(
    [
        'email' => $config['user_email'],
    ]
);

$userId = null;
if ($userByEmail->getOk()) {
    $userId = $userByEmail->getUser()->getId();
} else {
    throw new Exception('Cannot get user ID by email.');
}

// Отправить личное сообщение пользователю (используется его ID в workspace)
// https://api.slack.com/methods/chat.postMessage
$client->chatPostMessage(
    [
        'as_user' => true,
        'channel' => $userId,
        'text'    => 'test12345',
    ]
);

// Создать приватный канал с пользователем для нотификаций.
//
// conversations.create - public/private channel creation.
// conversations.open - direct message for 1 person or N persons.
$resp = $client->groupsCreate(
    [
        'name' => 'test-private-channel-for-notifications',
    ]
);

dump($resp);
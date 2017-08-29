<?php

namespace App\Commands;

use App\Users;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "start";

    /**
     * @var string Command Description
     */
    protected $description = "Comando para inicializar o Bot";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        $name = $arguments ? ' ' . $arguments : '';
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $this->replyWithMessage(['text' => "Olá" . $name . '! Bem-vindo ao nosso bot! Aqui estão os nossos comandos:']);
//        $this->replyWithMessage(['text' => json_encode($this->getUpdate())]);

        // This will update the chat status to typing...

        // This will prepare a list of available commands and send the user.
        // First, Get an array of all registered commands
        // They'll be in 'command-name' => 'Command Handler Class' format.
        $commands = $this->getTelegram()->getCommands();

        // Build the list
        $response = '';
        foreach ($commands as $name => $command) {
            $response .= sprintf('/%s - %s' . PHP_EOL, $name, $command->getDescription());
        }

        // Reply with the commands list
        $this->replyWithMessage(['text' => $response]);

        /* {"id":144068960,"is_bot":false,"first_name":"Seasky","username":"se45ky","language_code":"pt-BR"} */
        $user = $this->getUpdate()->getMessage()->getFrom();
        $newUser = Users::firstOrNew(['telegram_id' => $user->getId()]);
        $newUser->is_bot = $user->get('is_bot');
        $newUser->first_name = $user->getFirstName();
        $newUser->username = $user->getUsername();
        $newUser->language_code = $user->get('language_code');
        $newUser->save();


        // Trigger another command dynamically from within this command
        // When you want to chain multiple commands within one or process the request further.
        // The method supports second parameter arguments which you can optionally pass, By default
        // it'll pass the same arguments that are received for this command originally.
        $this->triggerCommand('subscribe');
//        $this->triggerCommand('help');
    }
}

<?php

namespace App\Commands;

use Telegram\Bot\Actions;

class StartCommand extends AbstractCommand
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

        $this->enableUser();
        $this->setVersion();

        // Trigger another command dynamically from within this command
        // When you want to chain multiple commands within one or process the request further.
        // The method supports second parameter arguments which you can optionally pass, By default
        // it'll pass the same arguments that are received for this command originally.
        $this->triggerCommand('subscribe');
//        $this->triggerCommand('help');
    }

    public function setVersion()
    {
        $keyboard = ['inline_keyboard' => [
            [[
                'text' => 'PT-BR - AA - Almeida & Atualizada', 'callback_data' => '/setVersion aa'
            ]],
            [[
                'text' => 'PT-BR - NVI - Nova Versao Internacional', 'callback_data' => '/setVersion nvi'
            ]],
            [[
                'text' => 'PT-BR - ACF - Almeida Corrigida Fiel', 'callback_data' => '/setVersion acf'
            ]],
            [[
                'text' => 'EN-EN - KJV - King James Version', 'callback_data' => '/setVersion acf'
            ]],
            [[
                'text' => 'EN-EN - BBE - Basic English', 'callback_data' => '/setVersion bbe'
            ]],
            [[
                'text' => 'ES-ES - RVR - Reina Valera', 'callback_data' => '/setVersion rvr'
            ]],
        ]];

        $reply_markup = json_encode($keyboard);
        $this->replyWithMessage([
            'parse_mode' => 'Markdown',
            'text' => 'Selecione sua versao biblica de preferencia:',
            'reply_markup' => $reply_markup
        ]);
    }
}

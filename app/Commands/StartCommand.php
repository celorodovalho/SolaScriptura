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
    protected $description = "Para inicializar o Bot";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        try {
            $name = $arguments ? ' ' . $arguments : '';
            if (!$name) {
                $name = $this->getTelegramUser()->getFirstName();
            }
            // This will update the chat status to typing...
            $this->replyWithChatAction(['action' => Actions::TYPING]);

            $this->replyWithMessage([
                'text' => "Olá" . $name . '! Bem-vindo ao nosso bot! Aqui estão os nossos comandos:'
            ]);

            $this->listCommands();
            $this->enableUser();

            // Trigger another command dynamically from within this command
            // When you want to chain multiple commands within one or process the request further.
            // The method supports second parameter arguments which you can optionally pass, By default
            // it'll pass the same arguments that are received for this command originally.
            $this->triggerCommand('subscribe');
            $this->triggerCommand('version');
        } catch (\Exception $e) {
            $this->alertUser();
            $this->log('EXCEPTION', $e->getMessage());
        }
    }
}

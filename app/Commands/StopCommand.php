<?php

namespace App\Commands;

use Telegram\Bot\Actions;
use Telegram\Bot\Exceptions\TelegramOtherException;

class StopCommand extends AbstractCommand
{
    /**
     * @var string Command Name
     */
    protected $name = "stop";

    /**
     * @var string Command Description
     */
    protected $description = "Comando para parar o Bot";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        try {
            $this->checkPermission();
            $name = $arguments ? ' ' . $arguments : '';
            $this->replyWithChatAction(['action' => Actions::TYPING]);
            $this->replyWithMessage(['text' => "Adeus " . $name . " :'("]);
            $this->disableUser();
            $this->replyWithMessage(['text' => "Para ativar o Bot novamente, use o comando /start"]);
        } catch (TelegramOtherException $e) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                'text' => $e->getMessage()
            ]);
        }
    }
}

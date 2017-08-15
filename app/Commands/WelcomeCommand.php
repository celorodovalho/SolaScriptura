<?php

namespace App\Commands;

use Telegram;
use Telegram\Bot\Commands\Command;

/**
 * Class HelpCommand.
 */
class WelcomeCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'help';

    /**
     * @var string Command Description
     */
    protected $description = 'List all commands';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        $commands = $this->telegram->getCommands();

        $text = '';
        $keys = [];
        foreach ($commands as $name => $handler) {
            if ($name !== 'poke') {
                $text .= sprintf('/%s - %s' . PHP_EOL, $name, $handler->getDescription());
                $keys[] = '/' . $name;
            }
        }

        $reply_markup = Telegram::replyKeyboardMarkup([
            'keyboard' => [$keys],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);

//        $this->replyWithMessage(compact('text'));
        $this->replyWithMessage([
            'parse_mode' => 'Markdown',
            'text' => $text,
            'reply_markup' => $reply_markup
        ]);
    }
}
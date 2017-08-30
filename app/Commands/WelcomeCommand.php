<?php

namespace App\Commands;

use Telegram;

/**
 * Class HelpCommand.
 */
class WelcomeCommand extends AbstractCommand
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

        $replyMarkup = Telegram::replyKeyboardMarkup([
            'keyboard' => [$keys],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);

//        $this->replyWithMessage(compact('text'));
        $this->replyWithMessage([
            'parse_mode' => 'Markdown',
            'text' => $text.' => WELCOME '.json_encode($arguments),
            'reply_markup' => $replyMarkup
        ]);
    }
}

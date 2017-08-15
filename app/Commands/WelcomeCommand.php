<?php

namespace App\Commands;

use Telegram\Bot\Commands\Command;

/**
 * Class HelpCommand.
 */
class WelcomeCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'welcome';

    /**
     * @var string Command Description
     */
    protected $description = 'Welcome command!';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
//        $this->replyWithMessage(compact('text'));
    }
}

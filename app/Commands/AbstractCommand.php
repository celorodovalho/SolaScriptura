<?php

namespace App\Commands;

use App\Users;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Exceptions\TelegramOtherException;
use Illuminate\Support\Facades\Log;
use Telegram;

class AbstractCommand extends Command
{
    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
    }

    public function enableUser()
    {
        try {
            $user = $this->getTelegramUser();
            $newUser = Users::withTrashed()->firstOrNew(['telegram_id' => $user->getId()]);
            $newUser->is_bot = $user->get('is_bot');
            $newUser->first_name = $user->getFirstName();
            $newUser->last_name = $user->getLastName();
            $newUser->username = $user->getUsername();
            $newUser->language_code = $user->get('language_code');
            if ($newUser->trashed()) {
                $newUser->restore();
            } else {
                $newUser->save();
            }
        } catch (\Exception $e) {
            $this->alertUser();
            $this->log('EXCEPTION', $e->getMessage());
        }
    }

    public function disableUser()
    {
        try {
            $user = $this->getTelegramUser();
            $newUser = Users::withTrashed()->where('telegram_id', $user->getId())->first();
            $newUser->delete();
        } catch (\Exception $e) {
            $this->alertUser();
            $this->log('EXCEPTION', $e->getMessage());
        }
    }

    public function isUserActive()
    {
        try {
            $user = $this->getTelegramUser();
            $newUser = Users::withTrashed()->where('telegram_id', $user->getId())->first();
            if ($newUser) {
                return !$newUser->trashed();
            }
        } catch (\Exception $e) {
            $this->alertUser();
            $this->log('EXCEPTION', $e->getMessage());
        }
        return false;
    }

    public function checkPermission()
    {
        if (!$this->isUserActive()) {
            switch (class_basename(__CLASS__)) {
                case 'StartCommand':
                    break;
                default:
                    throw new TelegramOtherException('Antes de utilizar os comandos, vc precisa ativar o Bot: /start');
            }
        }
    }

    public function getTelegramUser()
    {
        $update = $this->getUpdate();
        $user = $update->get('from');
        if (!$user) {
            $user = $update->getMessage()->getFrom();
        } else {
            $user = new Telegram\Bot\Objects\User($user);
        }
        return $user;
    }

    /**
     * @return Users
     */
    public function getUser()
    {
        $tUser = $this->getTelegramUser();
        return Users::where('telegram_id', $tUser->getId())->first();
    }

    public function listCommands()
    {
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
    }

    public function log($code, $msg)
    {
        Log::info("$code: " . json_encode($msg));
        Log::info("BACKTRACE: " . json_encode(debug_backtrace()));
        Telegram::sendMessage([
            'chat_id' => '144068960',
            'text' => "$code:\r\n" .
                substr(json_encode($msg), 0, 4096)
        ]);
    }

    public function alertUser()
    {
        $this->replyWithMessage([
            'parse_mode' => 'Markdown',
            'text' => 'Um erro inesperado aconteceu, contacte: @se45ky'
        ]);
    }
}

<?php

namespace App\Commands;

use App\Users;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Exceptions\TelegramOtherException;

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
            $user = $this->getUpdate()->getMessage()->getFrom();
            $newUser = Users::withTrashed()->firstOrNew(['telegram_id' => $user->getId()]);
            $newUser->is_bot = $user->get('is_bot');
            $newUser->first_name = $user->getFirstName();
            $newUser->last_name = $user->getLastName();
            $newUser->username = $user->getUsername();
            $newUser->language_code = $user->get('language_code');
            $newUser->status = 1;
            if ($newUser->trashed()) {
                $newUser->restore();
            } else {
                $newUser->save();
            }
        } catch (\Exception $e) {
            $this->replyWithMessage(['text' => $e->getMessage()]);
        }
    }

    public function disableUser()
    {
        try {
            $user = $this->getUpdate()->getMessage()->getFrom();
            $newUser = Users::withTrashed()->where('telegram_id', $user->getId())->first();
            $newUser->delete();
        } catch (\Exception $e) {
            $this->replyWithMessage(['text' => $e->getMessage()]);
        }
    }

    public function isUserActive()
    {
        try {
            $user = $this->getUpdate()->getMessage()->getFrom();
            $newUser = Users::withTrashed()->where('telegram_id', $user->getId())->first();
            return !$newUser->trashed();
        } catch (\Exception $e) {
            $this->replyWithMessage(['text' => $e->getMessage()]);
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
}
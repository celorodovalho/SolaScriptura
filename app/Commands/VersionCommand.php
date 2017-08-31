<?php

namespace App\Commands;

use Telegram\Bot\Actions;
use Telegram\Bot\Exceptions\TelegramOtherException;

class VersionCommand extends AbstractCommand
{
    /**
     * @var string Command Name
     */
    protected $name = "version";

    /**
     * @var string Command Description
     */
    protected $description = "Para escolher a versao";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        try {
            $this->checkPermission();
            $this->replyWithChatAction(['action' => Actions::TYPING]);
            if (!empty($arguments)) {
                $this->setVersion($arguments);
            } else {
                $this->listVersion();
            }
        } catch (TelegramOtherException $e) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                'text' => $e->getMessage()
            ]);
        }
    }

    public function setVersion($version)
    {
        $user = $this->getUser();
        $user->version = $version;
        return $user->save();
    }

    public function listVersion()
    {
        $keyboard = ['inline_keyboard' => [
            [[
                'text' => 'AA - Almeida & Atualizada', 'callback_data' => '/version aa'
            ]],
            [[
                'text' => 'NVI - Nova Versao Internacional', 'callback_data' => '/version nvi'
            ]],
            [[
                'text' => 'ACF - Almeida Corrigida Fiel', 'callback_data' => '/version acf'
            ]],
            [[
                'text' => 'KJV - King James Version', 'callback_data' => '/version acf'
            ]],
            [[
                'text' => 'BBE - Basic English', 'callback_data' => '/version bbe'
            ]],
            [[
                'text' => 'RVR - Reina Valera', 'callback_data' => '/version rvr'
            ]],
        ]];

        $replyMarkup = json_encode($keyboard);
        $this->replyWithMessage([
            'parse_mode' => 'Markdown',
            'text' => 'Selecione a versao biblica de sua preferencia:',
            'reply_markup' => $replyMarkup
        ]);
    }
}

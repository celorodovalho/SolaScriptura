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
     * @var array
     */
    protected $versions = [
        'aa' => 'AA - Almeida & Atualizada',
        'nvi' => 'NVI - Nova Versao Internacional',
        'acf' => 'ACF - Almeida Corrigida Fiel',
        'kjv' => 'KJV - King James Version',
        'bbe' => 'BBE - Basic English',
        'rvr' => 'RVR - Reina Valera',
    ];

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
                $this->replyWithMessage([
                    'parse_mode' => 'Markdown',
                    'text' => 'Versao "' . $this->versions[$arguments] . '" selecionada.',
                ]);
            } else {
                $this->listVersion();
            }
        } catch (\Exception $e) {
            $this->alertUser();
            $this->log('EXCEPTION', $e->getMessage());
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
        $keyboard = ['inline_keyboard' => []];
        foreach ($this->versions as $version => $name) {
            $keyboard['inline_keyboard'][0] = [[
                'text' => $name, 'callback_data' => '/version ' . $version
            ]];
        }

        $replyMarkup = json_encode($keyboard);
        $this->replyWithMessage([
            'parse_mode' => 'Markdown',
            'text' => 'Selecione a versao biblica de sua preferencia:',
            'reply_markup' => $replyMarkup
        ]);
    }
}

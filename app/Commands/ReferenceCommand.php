<?php

namespace App\Commands;

use App\Verses;
use Illuminate\Support\Facades\Log;
use Telegram;
use Telegram\Bot\Actions;
use Telegram\Bot\Exceptions\TelegramOtherException;

class ReferenceCommand extends AbstractCommand
{
    /**
     * @var string Command Name
     */
    protected $name = "ref";

    /**
     * @var string Command Description
     */
    protected $description = 'Informe a referencia, ex: Joao 3:16-17';

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        try {
            $this->checkPermission();
            $arguments = trim($arguments);
            $versiculo = $arguments;
            $this->replyWithChatAction(['action' => Actions::TYPING]);
            if (empty($arguments)) {
                $replyMarkup = Telegram::forceReply(['selective' => true]);
                return $this->replyWithMessage([
                    'text' => 'Informe a referencia, ex: Joao 3:16-17' . "\r\n" .
                        '[/ref]'
                    ,
                    'reply_markup' => $replyMarkup
                ]);
            }

            $arguments = explode(' ', $arguments);
            $book = $arguments[0];
            $chapter = $arguments[1];
            $chapter = explode(':', $chapter);
            $verses = $chapter[1];
            $chapter = $chapter[0];

            $user = $this->getUser();
            if (!$user->version) {
                throw new \Exception('Primeiro voce deve informar sua versao.', 10001);
            }

            $response = Verses::ref($user->version, $book, $chapter, $verses);

            if (empty($response)) {
                throw new TelegramOtherException('Referencia nao encontrada.');
            }

            $return = ['*' . $versiculo . '*' . "\r\n"];
            foreach ($response as $verse) {
                $return[] = '*' . $verse['verse'] . ')* ' . html_entity_decode(trim($verse['text'])) . "\r\n";
            }

            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                'text' => implode($return),
            ]);
        } catch (TelegramOtherException $e) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                'text' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            switch ($e->getCode()) {
                case 10001:
                    $this->replyWithMessage([
                        'parse_mode' => 'Markdown',
                        'text' => $e->getMessage()
                    ]);
                    $this->triggerCommand('version');
                    break;
                default:
                    $this->alertUser();
                    $this->log('EXCEPTION', $e->getMessage());
            }

        }
        return null;
    }
}

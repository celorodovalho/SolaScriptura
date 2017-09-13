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
            $version = isset($arguments[2]) ? $arguments[2] : null;
            $versions = VersionCommand::$versions;
            $versiculo = $arguments[0] . ' ' . $arguments[1];

            if (empty($version)) {
                $user = $this->getUser();
                if (!$user->version) {
                    throw new \Exception('Primeiro voce deve informar sua versao.', 10001);
                } else {
                    $version = $user->version;
                }
            } else {
                $version = strtolower($version);
                if (!array_key_exists($version, $versions)) {
                    throw new \Exception('Primeiro voce deve informar sua versao.', 10001);
                }
            }

            $response = Verses::ref($version, $book, $chapter, $verses);

            if (empty($response)) {
                throw new TelegramOtherException('Referencia nao encontrada.');
            }

            $return = ['*' . $versiculo . ' [' . strtoupper($version) . ']*' . "\r\n"];
            foreach ($response as $verse) {
                $return[] = '*' . $verse['verse'] . ')* ' . html_entity_decode(trim($verse['text'])) . "\r\n";
            }

            $inlineKeyboard = ['inline_keyboard' => []];
//                [[
//                    'text' => 'Moves', 'callback_data' => '/poke moves ' . $pokemon['id']
//                ]],
//                [[
//                    'text' => 'Formas', 'callback_data' => '/poke forms ' . $pokemon['id']
//                ]],
////                [[
////                    'text' => 'Espécies', 'callback_data' => '/poke species ' . $pokemon['id']
////                ]],
//                [[
//                    'text' => 'Evoluções', 'callback_data' => '/poke evolution ' . $pokemon['id']
//                ]],
//            ]];
            foreach ($versions as $key => $desc) {
                $inlineKeyboard['inline_keyboard'][] = [[
                    'text' => $desc, 'callback_data' => '/ref ' . $versiculo . ' ' . $key
                ]];
            }

            $replyMarkup = json_encode($inlineKeyboard);
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                'text' => implode($return),
            ]);
        } catch (TelegramOtherException $e) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                'text' => $e->getMessage(),
                'reply_markup' => $replyMarkup
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

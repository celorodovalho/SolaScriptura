<?php

namespace App\Commands;

use Illuminate\Support\Facades\Log;
use Telegram;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class ReferenceCommand extends Command
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
            $arguments = trim($arguments);
            // This will update the chat status to typing...
            $this->replyWithChatAction(['action' => Actions::TYPING]);
            if (empty($arguments)) {
                //throw new Telegram\Bot\Exceptions\TelegramOtherException('Você precisa informar o nome ou número do Pokémon, ex: /dex pikachu');
                $reply_markup = Telegram::forceReply(['selective' => true]);
                return $this->replyWithMessage([
                    //'parse_mode' => 'Markdown',
                    'text' => 'Informe a referencia, ex: Joao 3:16-17' . "\r\n" .
                        '[/ref]'
                    ,
                    'reply_markup' => $reply_markup
                ]);
            }

            $arguments = str_replace(' ', '+', $arguments);

            $response = $this->simpleCurl('https://bible-api.com/' . $arguments, null, ['translation' => 'almeida']);
            Log::info('DEX-ERRO1: ' . json_encode($response));

            if (strlen($response) > 21) {
                $response = json_decode($response, true);
            } else {
                throw new Telegram\Bot\Exceptions\TelegramOtherException('Referencia nao encontrada.');
            }


            $return = ['*' . $response['reference'] . '*' . "\r\n"];
            foreach ($response['verses'] as $verse) {
                $return[] = '*' . $verse['verse'] . ')* ' . trim($verse['text']) . "\r\n";
            }

            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                'text' => implode($return),
                //'reply_markup' => $reply_markup
            ]);
        } catch (Telegram\Bot\Exceptions\TelegramOtherException $e) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                'text' => $e->getMessage()
            ]);
            return null;
        } catch (\Exception $e) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                'text' => 'Sorry. Try again later.'
            ]);
            $response = Telegram::sendMessage([
                'parse_mode' => 'Markdown',
                'chat_id' => '144068960',
                'text' => $e->getMessage()/* . "\r\n" .
                    $e->getFile() . "\r\n" .
                    $e->getLine() . "\r\n"*/
            ]);
            Log::info('DEX-ERRO1: ' . json_encode($e->getMessage()));
            Log::info('DEX-ERRO2: ' . $e->getTraceAsString());
        }

    }

    public function simpleCurl($url, $post = array(), $get = array())
    {
        Log::info('DEX-ERRO3: ' . $url);
        $url = explode('?', $url, 2);
        if (count($url) === 2) {
            $temp_get = array();
            parse_str($url[1], $temp_get);
            $get = array_merge($get, $temp_get);
        }
        $url = $url[0] . ($get ? '?' . http_build_query($get) : '');
        Log::info('DEX-ERRO4: ' . $url);
        $ch = curl_init($url);

        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return curl_exec($ch);
    }
}
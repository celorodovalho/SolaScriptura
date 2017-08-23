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

            $response = \App\Verses::ref('nvi', $book, $chapter, $verses);

            Log::info('RESPONSE---->: ' . json_encode($response));

//             $this->replyWithMessage([
//                 'parse_mode' => 'Markdown',
//                 'text' => json_encode([$response]),
//             ]);


//            $response = $this->simpleCurl('https://bible-api.com/' . $arguments, null, ['translation' => 'almeida']);

            if (empty($response)) {
                throw new Telegram\Bot\Exceptions\TelegramOtherException('Referencia nao encontrada.');
            }

            $return = ['*' . $versiculo . '*' . "\r\n"];
            foreach ($response as $verse) {
                $return[] = '*' . $verse['verse'] . ')* ' . html_entity_decode(trim($verse['text'])) . "\r\n";
            }

            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                'text' => implode($return),
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

            Log::info('ERRO1: ' . json_encode($e->getMessage()));
            Log::info('ERRO2: ' . $e->getTraceAsString());
        }
        return null;
    }

    /**
     * @param $url
     * @param array $post
     * @param array $get
     * @return mixed
     */
    public function simpleCurl($url, $post = array(), $get = array())
    {
        $url = explode('?', $url, 2);
        if (count($url) === 2) {
            $tempGet = array();
            parse_str($url[1], $tempGet);
            $get = array_merge($get, $tempGet);
        }
        $url = $url[0] . ($get ? '?' . http_build_query($get) : '');
        $chr = curl_init($url);

        if ($post) {
            curl_setopt($chr, CURLOPT_POST, 1);
            curl_setopt($chr, CURLOPT_POSTFIELDS, json_encode($post));
        }

        curl_setopt($chr, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($chr, CURLOPT_RETURNTRANSFER, true);
        return curl_exec($chr);
    }
}

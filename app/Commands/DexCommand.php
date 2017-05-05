<?php

namespace App\Commands;

use Illuminate\Support\Facades\Log;
use Telegram;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use PokePHP\PokeApi;

class DexCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "dex";

    /**
     * @var string Command Description
     */
    protected $description = "Search Pokémon by name or ID";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        try {
            // This will update the chat status to typing...
            $this->replyWithChatAction(['action' => Actions::TYPING]);
            if (empty($arguments)) {
                //throw new Telegram\Bot\Exceptions\TelegramOtherException('Você precisa informar o nome ou número do Pokémon, ex: /dex pikachu');
                $reply_markup = Telegram::forceReply(['selective' => true]);
                return $this->replyWithMessage([
                    //'parse_mode' => 'Markdown',
                    'text' => 'Send the Pokémon ID or Name' . "\r\n" .
                        '[/dex]'
                    ,
                    'reply_markup' => $reply_markup
                ]);
            }

            $api = new PokeApi;
            $pokemon = $api->pokemon(strtolower(trim($arguments)));
//        https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/25.png
            if (strlen($pokemon) > 21) {
                $pokemon = json_decode($pokemon, true);
            } else {
                throw new Telegram\Bot\Exceptions\TelegramOtherException('Pokémon not found. I know only until #721.');
            }
            if (is_string($pokemon)) {
                throw new \Exception($pokemon);
            }
            if (!array_key_exists('id', $pokemon)) {
                throw new \Exception(json_encode($pokemon));
//                return null;
            }

            $pokemonText = '*Nome:* ' . ucwords($pokemon['name']) . "\r\n" .
                '*ID:* ' . $pokemon['id'] . "\r\n";
            $types = [];
            foreach ($pokemon['types'] as $type) {
                $types[] = ucwords(str_replace('-', ' ', $type['type']['name']));
            }
            $pokemonText .= '*Tipo:* ' . implode(' / ', $types) . "\r\n";
            $abilities = [];
            foreach ($pokemon['abilities'] as $ability) {
                $abilities [] = ucwords(str_replace('-', ' ', $ability['ability']['name']));
            }
            $pokemonText .= '*Hab.:* ' . implode(' / ', $abilities) . "\r\n";
            $pokemonText .= '*Peso:* ' . $pokemon['weight'] . "\r\n";
            $pokemonText .= '*Altura:* ' . $pokemon['height'] . "\r\n";
            $pokemonText .= '*XP Base:* ' . $pokemon['base_experience'] . "\r\n";
            $pokemonText .= '*---STATS---* ' . "\r\n";
            foreach ($pokemon['stats'] as $stat) {
                $pokemonText .= '*' . ucwords(str_replace('-', ' ', $stat['stat']['name'])) . ':* ' . $stat['base_stat'] . "\r\n";
            }
            /*$pokemonText .= '*---MOVES---* ' . "\r\n";
            $moves = [];
            $otherMoves = [];
            foreach ($pokemon['moves'] as $move) {
                $moveName = ucwords(str_replace('-', ' ', $move['move']['name']));
                foreach ($move['version_group_details'] as $version) {
                    $moveType = $version['move_learn_method']['name'];
                    if ($moveType === 'level-up') {
                        $moves[$version['level_learned_at']] = $moveName . ' (' . (ucwords(str_replace('-', ' ', $version['version_group']['name']))) . ')';
                    } else {
                        if ($version['move_learn_method']['name'] === 'machine') {
                            $method = 'TM/HM';
                        } else {
                            $method = ucfirst($version['move_learn_method']['name']);
                        }
                        $otherMoves[$moveName] = '*' . $method . '*: ' . $moveName . ' (' . (ucwords(str_replace('-', ' ', $version['version_group']['name']))) . ')';
                    }
                }
            }
            //AIzaSyAWkvwsYSvGawyzw1a_RcLimx0mDxcaeUk
            //AIzaSyDSmgf5fKbJHvACVNHdipDcRneBRwMVAk8
            ksort($moves);
            foreach ($moves as $key => $move) {
                $pokemonText .= '*Lv. ' . $key . ':* ' . $move . "\r\n";
            }*/
//            $response = $this->replyWithPhoto([
////            'chat_id' => '144068960',
//                'photo' => 'http://marcelorodovalho.com.br/pokemon/sugimore/' . $pokemon['id'] . '.png',
//            ]);
            $photo = $this->simpleCurl(
                'https://www.googleapis.com/urlshortener/v1/url?key=AIzaSyDSmgf5fKbJHvACVNHdipDcRneBRwMVAk8',
                [
                    'longUrl' => 'http://marcelorodovalho.com.br/pokemon/sugimore/' . $pokemon['id'] . '.png'
                ]
            );
//            Log::info($photo);
            $photo = json_decode($photo, true);
            $pokemonText .= $photo['id'];

            $keyboard = ['inline_keyboard' => [
//                [[
//                    'text' => 'Habilidades', 'callback_data' => '/poke abilities ' . $pokemon['id']
//                ]],
                [[
                    'text' => 'Moves', 'callback_data' => '/poke moves ' . $pokemon['id']
                ]],
                [[
                    'text' => 'Formas', 'callback_data' => '/poke forms ' . $pokemon['id']
                ]],
//                [[
//                    'text' => 'Espécies', 'callback_data' => '/poke species ' . $pokemon['id']
//                ]],
                [[
                    'text' => 'Evoluções', 'callback_data' => '/poke evolution ' . $pokemon['id']
                ]],
            ]];

            $reply_markup = json_encode($keyboard);
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                'text' => $pokemonText,
                'reply_markup' => $reply_markup
            ]);
            /*
            asort($otherMoves);
            $otherMoves = implode("\r\n", $otherMoves) . "\r\n";
            $this->replyWithMessage(['parse_mode' => 'Markdown', 'text' => $otherMoves]);
            */
//        $arguments

            // Reply with the commands list
            //$this->replyWithMessage(['text' => $response]);
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
        $url = explode('?', $url, 2);
        if (count($url) === 2) {
            $temp_get = array();
            parse_str($url[1], $temp_get);
            $get = array_merge($get, $temp_get);
        }

        $ch = curl_init($url[0] . ($get ? '?' . http_build_query($get) : ''));

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return curl_exec($ch);
    }
}
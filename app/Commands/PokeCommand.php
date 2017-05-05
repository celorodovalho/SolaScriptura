<?php

namespace App\Commands;

use Illuminate\Support\Facades\Log;
use Telegram;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use PokePHP\PokeApi;

class PokeCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'poke';

    /**
     * @var string Command Description
     */
    protected $description = 'Info details about pokemon';

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        try {
            $this->replyWithChatAction(['action' => Actions::TYPING]);
            if (!$arguments) {
                throw new Telegram\Bot\Exceptions\TelegramOtherException('Please, use the /dex command.');
            }
            $api = new PokeApi;
            $arguments = explode(' ', $arguments);
            $response = '';
            switch ($arguments[0]) {
//                case 'ability':
//                    break;
//                case 'abilities':
//                    break;
                case 'moves':
                    $pokemon = $api->pokemon(strtolower(trim($arguments[1])));
                    $pokemon = json_decode($pokemon, true);
                    $response = self::moves($pokemon);
                    break;
                case 'forms':
                    $pokemon = $api->pokemonForm($arguments[1]);
                    $pokemon = json_decode($pokemon, true);
                    $response = $this->forms($pokemon);
                    break;
                //mais detalhes
//                case 'species':
//                    break;
                case 'evolution':
                    $pokemon = $api->pokemonSpecies($arguments[1]);
                    $pokemon = json_decode($pokemon, true);
                    $response = self::evolution($pokemon, $api);
                    break;
            }
            if ($response) {
                $responses = str_split($response, 4200);
                $this->replyWithChatAction(['action' => Actions::TYPING]);
                foreach ($responses as $response) {
                    $this->replyWithMessage(['parse_mode' => 'Markdown', 'text' => $response]);
                }
            }
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
            Telegram::sendMessage([
                'parse_mode' => 'Markdown',
                'chat_id' => '144068960',
                'text' => $e->getMessage()
            ]);
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
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return curl_exec($ch);
    }

    public static function moves($pokemon)
    {
        $response = '';
        $moves = [
            'level-up' => [],
            'TM/HM' => [],
            'Tutor' => [],
            'Egg' => [],
        ];
        foreach ($pokemon['moves'] as $move) {
            $moveName = ucwords(str_replace('-', ' ', $move['move']['name']));
            foreach ($move['version_group_details'] as $version) {
                $moveType = $version['move_learn_method']['name'];
                $versions = ucwords(str_replace('-', ' ', $version['version_group']['name']));
                $versions = explode(' ', $versions);
                $versionsS = '';
                foreach ($versions as $w) {
                    $versionsS .= $w[0];
                }
                switch ($moveType) {
                    case 'level-up':
                        $key = 'Lv ' . str_pad($version['level_learned_at'], 3, '0', STR_PAD_LEFT) . ' ';
                        $newMoveName = $version['level_learned_at'] . ' - ' . $moveName;
                        break;
                    case 'machine':
                        $moveType = 'TM/HM';
                    default:
                        $newMoveName = $moveName;
                        $key = $moveName . $moveType;
                        $moveType = ucfirst($moveType);
                        break;
                }
                if (array_key_exists($key . $newMoveName, $moves[$moveType])) {
                    $moves[$moveType][$key . $newMoveName] .= ' ' . $versionsS;
                } else {
                    $moves[$moveType][$key . $newMoveName] = $newMoveName . ' - ' . $versionsS;
                }
            }
        }
        foreach ($moves as $key => $move) {
            if (!empty($move)) {
                if ($key === 'level-up') {
                    ksort($move);
                } else {
                    asort($move);
                }
                $response .= "\r\n" . '*--' . $key . '--*' . "\r\n";
                $response .= implode("\r\n", $move);
            }
        }
        return $response;
    }

    public function forms($pokemon)
    {
        foreach ($pokemon['sprites'] as $key => $pk) {
            if (strpos($key, 'back') === false) {
                $text = ucfirst(str_replace(['front_', 'back_'], '', $key)) . "\r\n";
                $photo = $this->simpleCurl(
                    'https://www.googleapis.com/urlshortener/v1/url?key=AIzaSyDSmgf5fKbJHvACVNHdipDcRneBRwMVAk8',
                    [
                        'longUrl' => $pk
                    ]
                );
                $photo = json_decode($photo, true);
                $text .= $photo['id'];

                $this->replyWithMessage([
                    'text' => $text,
                ]);
            }
        }
        return '';
    }

    public static function evolution($pokemon, $api)
    {
        $evoChain = $pokemon['evolution_chain']['url'];
        $id = substr(strrchr(rtrim($evoChain, '/'), '/'), 1);
        $pokemon = $api->evolutionChain($id);
        $pokemon = json_decode($pokemon, true);
        $evos = [];
        self::evolveTo($pokemon['chain'], $evos);
        return implode(' - ', $evos);
    }

    public static function evolveTo($chain, &$evos)
    {
        $evos[] = ucfirst($chain['species']['name']);
        /*$evos[$chain['species']['name']] = [
            'name' => $chain['species']['name']
        ];
        if (!empty($chain['evolution_details'])) {
            switch ($chain['evolution_details'][0]['trigger']['name']) {
                case 'level-up':
                    if (!empty($chain['evolution_details'][0]['min_level'])) {
                        $type = 'Lv. ' . $chain['evolution_details'][0]['min_level'];
                    }
                    if (!empty($chain['evolution_details'][0]['time_of_day'])) {
                        $type = 'Level Up in ' . ucfirst($chain['evolution_details'][0]['time_of_day']) . ' Happiness ' . $chain['evolution_details'][0]['min_happiness'];
                    }
                    if (!empty($chain['evolution_details'][0]['location'])) {
                        $type = 'Level Up in ' . ucwords(str_replace('-', ' ', $chain['evolution_details'][0]['location']['name']));
                    }
                    if (!empty($chain['evolution_details'][0]['known_move_type'])) {
                        $type = 'Level Up when ' . ucwords(str_replace('-', ' ', $chain['evolution_details'][0]['known_move_type']['name']));
                    }
                    break;
                case 'use-item':
                    $type = 'Item: ' . $chain['evolution_details'][0]['item']['name'];
                    break;
                case 'trade':
                    $type = 'Trade';
                    if (!empty($chain['evolution_details'][0]['held_item'])) {
                        $type .= ' holding ' . ucwords(str_replace('-', ' ', $chain['evolution_details'][0]['held_item']['name']));
                    }


                    break;
            }
            $evos[$chain['species']['name']]['type'] = $type;;
        }*/
        foreach ($chain['evolves_to'] as $key => $value) {
            self::evolveTo($value, $evos);
        }
    }
}
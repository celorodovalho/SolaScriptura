<?php
namespace App\Http\Controllers\Bot;

use App\Commands\GtsCommand;
use App\Commands\PokeCommand;
use App\Commands\StartCommand;
use App\Http\Controllers\Controller;
use PokePHP\PokeApi;
use Symfony\Component\DomCrawler\Crawler;
use Telegram;
use Illuminate\Support\Facades\Log;
use App\FriendCodes;

class DefaultController extends Controller
{
    public function show()
    {
        /*$update = new Telegram\Bot\Objects\Update([
            'update_id' => 9865464654,
            'message' => [
                'message_id' => 984546544,
                'from' => [
                    'id' => 144068960,
                    'first_name' => 'Marcelo Rodovalho'
                ],
                'date' => 2016,
                'chat' => [
                    'id' => 987654654,
                    'type' => 'private'
                ],

            ]
        ]);
        $teste = Telegram::getCommandBus()->execute('poke', 'moves 25', $update);
        dump($teste);die;*/

        $api = new PokeApi;
//        $pokemon = $api->pokemon('oddish');
        $pokemon = $api->pokemonSpecies('oddish');
        $pokemon = json_decode($pokemon, true);
        $response = PokeCommand::evolution($pokemon, $api);

//        $pokemon = json_decode($pokemon, true);
//        $response = PokeCommand::moves($pokemon);
        dump($response);die;
        return 'ok';
    }

    public function setWebhook()
    {
        $response = Telegram::setWebhook(['url' => 'https://br796.hostgator.com.br/~marce769/projects/pokemondsbot/public/index.php/297560032:AAH9-NrRxuTi7x_jrpcCZ-y3s8nMWzAu4Ls/webhook']);
        //$update = Telegram::commandsHandler(true);
        return $response;
    }

    public function removeWebhook()
    {
        $response = Telegram::removeWebhook();
        return 'ok';
    }

    public function getUpdates()
    {
        $updates = Telegram::getUpdates();
        die;
    }
}
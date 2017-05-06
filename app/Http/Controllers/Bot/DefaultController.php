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
        return 'ok';
    }

    public function setWebhook()
    {
        $response = Telegram::setWebhook(['url' => 'https://marcelorodovalho.com.br/rodovalhos-bot/public/index.php/335603197:AAE9-l0gWZa4vtikwOnMftilpbcHh1isy58/webhook']);
        //$update = Telegram::commandsHandler(true);
        return $response;
    }

    public function removeWebhook()
    {
        $response = Telegram::removeWebhook();
        dump($response);
        return 'ok';
    }

    public function getUpdates()
    {
        $updates = Telegram::getUpdates();
        die;
    }

    public function getWebhookInfo()
    {
        Telegram::commandsHandler(true);
        $updates = Telegram::getWebhookInfo();
        dump($updates);
        die;
    }

    public function getMe()
    {
        $updates = Telegram::getMe();
        dump($updates);
        die;
    }
}
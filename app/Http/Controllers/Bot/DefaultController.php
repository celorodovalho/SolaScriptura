<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram;

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

        Telegram::sendMessage([
            'parse_mode' => 'Markdown',
            'chat_id' => '144068960',
            'text' => '*UPDATE:*' . "\r\n" .
                $updates->getId()
        ]);

        Telegram::sendMessage([
            'parse_mode' => 'Markdown',
            'chat_id' => '-201366561',
            'text' => '*UPDATE:*' . "\r\n" .
                $updates->getId()
        ]);

        die;
    }

    public function sendMessage(Request $request)
    {
        $arrBody = $request->all();
        Log::info("Message: ", $arrBody);
        if (!empty($arrBody)) {
            Telegram::sendMessage([
                'parse_mode' => 'Markdown',
                'chat_id' => '-201366561',
                'text' => "⚠️ 2017-05-02 ➡️ \r\n" .
                    implode($arrBody)
            ]);
        }


        die;
    }
}
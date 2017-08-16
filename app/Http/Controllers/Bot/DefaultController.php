<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram;

/**
 * Class DefaultController
 * @package App\Http\Controllers\Bot
 */
class DefaultController extends Controller
{
    /**
     * @return string
     */
    public function show()
    {
        return 'ok';
    }

    /**
     * @return mixed
     */
    public function setWebhook()
    {
//        $response = Telegram::setWebhook(['url' => 'https://*****/'.env('TELEGRAM_BOT_TOKEN').'/webhook']);
        $response = Telegram::setWebhook(['url' => secure_url('webhook')]);
        //$update = Telegram::commandsHandler(true);
        return $response;
    }

    /**
     * @return string
     */
    public function removeWebhook()
    {
        $response = Telegram::removeWebhook();
        dump($response);
        return 'ok';
    }

    public function getUpdates()
    {
        $updates = Telegram::getUpdates();
        dump($updates);
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
        dump(secure_url('webhook'));
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
                'text' => implode("\r\n\r\n", $arrBody)
            ]);
        }
        die;
    }

    public function test(Request $request)
    {
//        dump(654);die;
        $book = \App\Verses::find(1)->books()->where('abbrev', 'gn')->first(); //
//        App\Post::find(1)->comments()->where('title', 'foo')->first();
        dump($book);
        die;
    }
}

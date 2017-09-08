<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use App\Users;
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


    public function sendMessageEverybody(Request $request)
    {
        $users = Users::all();
        foreach ($users as $user) {
            Telegram::sendMessage([
                'parse_mode' => 'Markdown',
                'chat_id' => $user->telegram_id,
                'text' => 'Ola, estamos com algumas atualizacoes. Vc pode conferir no comando /start. O comando /ref ' .
                    'nao estava disponivel na ultima semana, pedimos desculpas.'
            ]);
        }
        die;
    }

    public function test(Request $request)
    {
        try {

//        dump($dbt->getLibraryVerseinfo('ENGKJVC1DA', 'Gen', '1', '1', '2')); //getVerseStart($damId, $bookId, $chapterId)

//        dump(654);die;
//        $book = \App\Verses::find(1)->books()->where('abbrev', 'gn')->first(); //
//        App\Post::find(1)->comments()->where('title', 'foo')->first();
//        dump($book);
            $user = \App\Users::withTrashed()->where(['telegram_id' => 654564])->first();
//        $users->delete();
//        Users::where();//
//            if ($user->trashed()) {
//                $user->first_name = 'Tsdfeste';
//                $user->restore();
//            } else {
//                $user->save();
//            }
            dump($user);
            dump($user->trashed());
        } catch (\Exception $e) {
            dump($e);
        }

        die;
    }
}

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
        return Telegram::setWebhook(['url' => secure_url('webhook')]);
    }

    /**
     * @return string
     */
    public function removeWebhook()
    {
        Telegram::removeWebhook();
    }

    public function getWebhookInfo()
    {
        Telegram::commandsHandler(true);
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
        return null;
    }

    public function sendMessageEverybody(Request $request)
    {
        $users = Users::all();
        foreach ($users as $user) {
            try {
                Telegram::sendMessage([
                    'parse_mode' => 'Markdown',
                    'chat_id' => $user->telegram_id,
                    'text' => 'Olá, nosso Bot foi atualizado recentemente. Vc pode conferir no comando /start.' .
                        ' O comando /ref está disponível novamente. Obrigado por utilizar.'
                ]);
            } catch (\Exception $e) {
            }
        }
        return null;
    }
}

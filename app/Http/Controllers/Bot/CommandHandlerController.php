<?php
namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use Telegram;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Exceptions\TelegramOtherException;

class CommandHandlerController extends Controller
{
    public function webhook()
    {
        try {
            /**
             * @var $updates Telegram\Bot\Objects\Update
             * @var $update Telegram\Bot\Objects\Update
             */
            $update = Telegram::commandsHandler(true);
            /*$updates = Telegram::getWebhookUpdates();
            Telegram::sendMessage([
                'parse_mode' => 'Markdown',
                'chat_id' => '144068960',
                'text' => "*CommandHandlerController (update):*\r\n" .
                    '```text ' .
                    json_encode($updates) .
                    '```'
            ]);*/

//           throw new TelegramOtherException('Essa opção está em desenvolvimento no momento. Tente novamente outro dia. COMANDSHANDLER');
            $callbackQuery = $update->get('callback_query');
            $message = $update->getMessage();

//            if ($callbackQuery) {
//                $arguments = explode(' ', $callbackQuery->get('data'));
//                $command = array_shift($arguments);
//                $command = str_replace(['\/', '/'], '', $command);
//                $arguments = implode(' ', $arguments);
//
//                Telegram::sendMessage([
//                    'parse_mode' => 'Markdown',
//                    'chat_id' => '144068960',
//                    'text' => '*LOG:*' . "\r\n" .
//                        '`CMD:` ' . $command . "\r\n" .
//                        '`ARGS:` ' . $arguments . "\r\n"
//                ]);
//                Telegram::sendMessage([
//                    'parse_mode' => 'Markdown',
//                    'chat_id' => '144068960',
//                    'text' => '*UPDATE:*' . "\r\n" .
//                        self::array2ul($update)
//                ]);
//
//                return Telegram::getCommandBus()->execute($command, $arguments, $callbackQuery);
//            }
//            if ($message) {
//                $newMember = $message->getNewChatParticipant();
//                if ($newMember) {
//                    $name = $newMember->getFirstName();
//                    return Telegram::getCommandBus()->execute('start', $name, $update);
//                }
//                $replyToMessage = $message->getReplyToMessage();
//                if ($replyToMessage && strpos($replyToMessage, '[\/') !== false) {
//                    preg_match("/\[[^\]]*\]/", $replyToMessage->getText(), $matches);
//                    $cmd = str_replace(['[', ']'], '', $matches[0]);
//                    if ($cmd) {
//                        $arguments = explode(' ', $cmd);
//                        $command = array_shift($arguments);
//                        $command = str_replace(['\/', '/'], '', $command);
//                        $text = $message->getText();
//                        $text = str_replace(' ', '', $text);
//                        if (!ctype_digit($text)) {
//                            $text = $message->getText();
//                        }
//                        $arguments = implode(' ', $arguments) . ' ' . $text;
//                        return Telegram::getCommandBus()->execute($command, $arguments, $update);
//                    }
//                }
//            }
        } catch (\Exception $e) {
            Telegram::sendMessage([
                'parse_mode' => 'Markdown',
                'chat_id' => '144068960',
                'text' => '*DEU ERRO:*' .
                    '```text ' .
                    json_encode($e->getMessage()) . "\r\n" .
                    json_encode($e->getLine()) . "\r\n" .
                    json_encode($e->getFile()) . "\r\n" .
                    '```'
            ]);
            Log::info('CMHND-ERRO1: ' . $e);
            Log::info('CMHND-ERRO2: ' . json_encode($e->getTrace()));
        }

        return 'ok';
    }

    public static function array2ul($array)
    {
        if (!is_array($array)) $array = json_decode(json_encode($array), true);
        $out = '';
        foreach ($array as $key => $elem) {
            if (!is_array($elem)) {
                $out .= "`$key:` $elem\r\n";
            } else $out .= "```$key:```" . self::array2ul($elem);
        }
        return $out;
    }
}
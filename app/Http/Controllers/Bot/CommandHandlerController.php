<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Telegram;

/**
 * Class CommandHandlerController
 * @package App\Http\Controllers\Bot
 */
class CommandHandlerController extends Controller
{
    /**
     * @return string
     */
    public function webhook()
    {
        try {
            /**
             * @var $updates Telegram\Bot\Objects\Update
             * @var $update Telegram\Bot\Objects\Update
             */
            $update = Telegram::commandsHandler(true);

            $callbackQuery = $update->get('callback_query');
            $message = $update->getMessage();

            if ($callbackQuery) {
                $arguments = explode(' ', $callbackQuery->get('data'));
                $command = array_shift($arguments);
                $command = str_replace(['\/', '/'], '', $command);
                $arguments = implode(' ', $arguments);

                $callbackQuery = new Telegram\Bot\Objects\Update($callbackQuery);

                return Telegram::getCommandBus()->execute($command, $arguments, $callbackQuery);
            }
            if ($message) {
                $newMember = $message->getNewChatParticipant();
                if ($newMember) {
                    $name = $newMember->getFirstName();
                    return Telegram::getCommandBus()->execute('start', $name, $update);
                }
                $replyToMessage = $message->getReplyToMessage();
                if ($replyToMessage && strpos($replyToMessage, '[\/') !== false) {
                    preg_match("/\[[^\]]*\]/", $replyToMessage->getText(), $matches);
                    $cmd = str_replace(['[', ']'], '', $matches[0]);
                    if ($cmd) {
                        $arguments = explode(' ', $cmd);
                        $command = array_shift($arguments);
                        $command = str_replace(['\/', '/'], '', $command);
                        $text = $message->getText();
                        $text = str_replace(' ', '', $text);
                        if (!ctype_digit($text)) {
                            $text = $message->getText();
                        }
                        $arguments = implode(' ', $arguments) . ' ' . $text;
                        return Telegram::getCommandBus()->execute($command, $arguments, $update);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::info('ERROR5: ' . json_encode($e->getTrace()));
        }

        return 'ok';
    }
}

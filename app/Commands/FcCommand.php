<?php

namespace App\Commands;

use Illuminate\Support\Facades\Log;
use Telegram;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Exceptions\TelegramOtherException;

class FcCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "fc";

    /**
     * @var string Command Description
     */
    protected $description = "List all Friend Codes [Version | Name | Code]";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        try {
            $update = $this->getUpdate();
            if (null == $update) {
                throw new TelegramOtherException('UPDATE NULO');
            }

            $users = \App\Models\NasLogins::getFC();

            $response = [];
            foreach ($users as $key => $user) {
                $response[$user['gamecd'] . $key] = '*' . $user['gamecd'] . '* | ' .
                    $user['devname'] . ' | `' .
                    $user['fc'] . '`' . "\r\n";
            }
            ksort($response);
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                'text' => implode($response)
            ]);

        } catch (TelegramOtherException $e) {
            $bt = debug_backtrace();
            $caller = array_shift($bt);
            Log::info(
                'CALLER: ' . "\r\n" .
                '=======> MESSAGE: ' . $e->getMessage() . "\r\n" .
                '=======> FILE: ' . $caller['file'] . "\r\n" .
                '=======> LINE: ' . $caller['line'] . "\r\n" .
                '=======> ARGS: ' . json_encode($caller['args']) . "\r\n" .
                '=======> TRACE: ' . $e->getTraceAsString() . "\r\n"
            );

            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                'text' => $e->getMessage()
            ]);
            Telegram::sendMessage([
                'parse_mode' => 'Markdown',
                'chat_id' => '144068960',
                'text' => $e->getMessage()
            ]);
            return null;
        } catch (\Exception $e) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                'text' =>
                    '```text ' .
                    $e->getMessage() .
                    '```'
                //'Desculpe. Nao consegui processar sua requisiçao. Tente novamente mais tarde.'
            ]);
        }
    }
}
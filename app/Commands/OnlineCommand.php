<?php

namespace App\Commands;

use App\Http\Middleware\Enums\GameNames;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Symfony\Component\DomCrawler\Crawler;

class OnlineCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "online";

    /**
     * @var string Command Description
     */
    protected $description = "Show how many player are online";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        try {
            // This will send a message using `sendMessage` method behind the scenes to
            // the user/chat id who triggered this command.
            // `replyWith<Message|Photo|Audio|Video|Voice|Document|Sticker|Location|ChatAction>()` all the available methods are dynamically
            // handled when you replace `send<Method>` with `replyWith` and use the same parameters - except chat_id does NOT need to be included in the array.
//        $this->replyWithMessage(['text' => '*Estão atualmente online:*']);

            // This will update the chat status to typing...
            $this->replyWithChatAction(['action' => Actions::TYPING]);

            $resposta = $this->simpleCurl(
                'http://requestmaker.com/requester.php',
                [
                    'url' => '104.131.93.87:9001',
                    'type' => 'GET'
                ]
            );

            $gamestats = GameNames::toArray();

            $resposta = explode('<html>', $resposta);
            $resposta = '<html>' . $resposta[1];

            $crawler = new Crawler($resposta);
            $table = $crawler->filter('table');

            // Build the list
            $response = [];
            $table->filter('tr')->each(function (Crawler $node, $i) use (&$response) {
                $response[] = [
                    $node->filter('td')->first()->text(),
                    $node->filter('td')->last()->text(),
                ];
            });

            if (count($response) > 1) {
                foreach ($response as $key => $resp) {
                    if (array_key_exists($resp[0], $gamestats)) {
                        $game = GameNames::cast($resp[0]);
                    } else {
                        $game = $resp[0];
                    }
                    $response[$key] = '*' . $game . "*:\t\t" . $resp[1] . "\r\n";
                }
                $this->replyWithMessage([
                    'parse_mode' => 'Markdown',
                    'text' => '*Online now:*' . "\r\n" .
                        implode('', $response)
                ]);

            } else {
                $this->replyWithMessage([
                    'parse_mode' => 'Markdown',
                    'text' => '*Nobody online*'
                ]);
            }

            // Trigger another command dynamically from within this command
            // When you want to chain multiple commands within one or process the request further.
            // The method supports second parameter arguments which you can optionally pass, By default
            // it'll pass the same arguments that are received for this command originally.
//        $this->triggerCommand('subscribe');
        } catch (\Exception $e) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                'text' => 'Sorry. Try again later.'
            ]);
            $response = Telegram::sendMessage([
                'parse_mode' => 'Markdown',
                'chat_id' => '144068960',
                'text' => $e->getMessage()/* . "\r\n" .
                    $e->getFile() . "\r\n" .
                    $e->getLine() . "\r\n"*/
            ]);
            Log::info('ONLINE-ERRO1: ' . json_encode($e->getMessage()));
            Log::info('ONLINE-ERRO2: ' . $e->getTraceAsString());
        }
    }

    public function simpleCurl($url, $post = array(), $get = array())
    {
        /*$url = explode('?', $url, 2);
        if (count($url) === 2) {
            $temp_get = array();
            parse_str($url[1], $temp_get);
            $get = array_merge($get, $temp_get);
        }*/

        $ch = curl_init($url . ($get ? "?" . http_build_query($get) : ''));

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept:*/*',
            'Accept-Encoding:gzip, deflate',
            'Accept-Language:pt-BR,pt;q=0.8,en-US;q=0.6,en;q=0.4',
            'Connection:keep-alive',
            'Content-Type:application/x-www-form-urlencoded',
            'Cookie:__utmt=1; __utma=183823909.1502575354.1479908325.1479908325.1479918231.2; __utmb=183823909.1.10.1479918231; __utmc=183823909; __utmz=183823909.1479918231.2.2.utmcsr=google|utmccn=(organic)|utmcmd=organic|utmctr=(not%20provided)',
            'Host:requestmaker.com',
            'Origin:http://requestmaker.com',
            'Referer:http://requestmaker.com/',
            'Save-Data:on',
            'User-Agent:Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.90 Safari/537.36',
            'X-Requested-With:XMLHttpRequest',
        ));

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return curl_exec($ch);
    }
}
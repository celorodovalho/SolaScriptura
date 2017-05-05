<?php

namespace App\Commands;

use Telegram;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;

class GtsCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "gts";

    /**
     * @var string Command Description
     */
    protected $description = "Show all pokémon available to trade in GTS";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        try {
            $this->replyWithChatAction(['action' => Actions::TYPING]);

            if (empty($arguments)) {
                $keyboard = ['inline_keyboard' => [
                    [[
                        'text' => 'Gen IV - DP/HG/SS', 'callback_data' => '/gts 4'
                    ]],
                    [[
                        'text' => 'Gen V - BW/BW2', 'callback_data' => '/gts 5'
                    ]]
                ]];

                $reply_markup = json_encode($keyboard);
                return $this->replyWithMessage([
                    'parse_mode' => 'Markdown',
                    'text' => 'Please, choose the Generation',
                    'reply_markup' => $reply_markup
                ]);
            }


            $resposta = $this->simpleCurl(
                'http://pkmnclassic.net/gts/',
                [
                    'ctl00$cpMain$grpGeneration' => $arguments == 4 ? 'rbGen4' : 'rbGen5'
                ]
            );
//            Log::info('GTS-ERRO1: ' . $resposta);

            $crawler = new Crawler($resposta);
            $response = [];
            $table = $crawler->filter('.gtsPokemonSummary')->each(function (Crawler $node, $i) use (&$response) {
                $shiny = (strpos($node->filter('.portrait img')->attr('src'),'-s/') !== false) ? '\[`Shiny`] ' : '';
                $player = trim(preg_replace('/\s+/', ' ', $node->filter('.pfFormValue')->eq(2)->text()));
                $available = trim(preg_replace('/\s+/', ' ', $node->filter('.pfFormValue')->eq(3)->text()));
                $avLv = trim(preg_replace('/\s+/', ' ', $node->filter('.pfFormValue')->eq(0)->text()));
                $item = trim(preg_replace('/\s+/', ' ', $node->filter('.pfFormValue')->eq(5)->text()));
                $wanted = trim(preg_replace('/\s+/', ' ', $node->filter('.pfFormValue')->eq(6)->text()));
                $waLv = trim(preg_replace('/\s+/', ' ', $node->filter('.pfFormValue')->eq(8)->text()));
                $response[] =
                    '\[`'.$player.'`] troca um '.$shiny.'*' . $available . ' ' . utf8_decode($avLv) . "*\r\n" .
                    (strlen($item) > 0 ? '*Com o item* \[`' . $item . "`]\r\n" : '') .
                    'por um * ' . $wanted . ' ' . utf8_decode($waLv).'*';
            });

            $response = 'Pokémon disponíveis Generation ' . ($arguments == 4 ? '4' : '5') . "\r\n\r\n" .
                implode("\r\n*=~=~=~=~=~=*\r\n", $response);

//            Log::info('GTS-ERRO2: ' . $response);
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                'text' => $response
            ]);
        } catch (\Exception $e) {
            $this->replyWithMessage([
                'parse_mode' => 'Markdown',
                'text' => 'Sorry. Try again later.'
            ]);
            $response = Telegram::sendMessage([
                'parse_mode' => 'Markdown',
                'chat_id' => '144068960',
                'text' => $e->getMessage()
            ]);
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
////            'Accept:*/*',
////            'Accept-Encoding:gzip, deflate',
////            'Accept-Language:pt-BR,pt;q=0.8,en-US;q=0.6,en;q=0.4',
////            'Connection:keep-alive',
////            'Content-Type:application/x-www-form-urlencoded',
////            'Cookie:__utmt=1; __utma=183823909.1502575354.1479908325.1479908325.1479918231.2; __utmb=183823909.1.10.1479918231; __utmc=183823909; __utmz=183823909.1479918231.2.2.utmcsr=google|utmccn=(organic)|utmcmd=organic|utmctr=(not%20provided)',
////            'Host:requestmaker.com',
////            'Origin:http://requestmaker.com',
////            'Referer:http://requestmaker.com/',
////            'Save-Data:on',
////            'User-Agent:Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.90 Safari/537.36',
////            'X-Requested-With:XMLHttpRequest',
//
            'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Encoding:gzip, deflate',
            'Accept-Language:pt-BR,pt;q=0.8,en-US;q=0.6,en;q=0.4',
            'Cache-Control:max-age=0',
            'Connection:keep-alive',
//            'Content-Length:17892',
            'Content-Type:application/x-www-form-urlencoded',
            'Cookie:ASP.NET_SessionId=3jadoqbx2hocq3x0ro0ydsht; pixelRatio=1',
            'Host:pkmnclassic.net',
            'Origin:http://pkmnclassic.net',
            'Referer:http://pkmnclassic.net/gts/',
            'Save-Data:on',
            'Upgrade-Insecure-Requests:1',
            'User-Agent:Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.90 Safari/537.36'
        ));


        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            '__VIEWSTATE=%2FwEPDwULLTE1OTIzMTc1MzUPZBYCZg9kFgxmD2QWBAIBDzLNAQABAAAA%2F%2F%2F%2F%2FwEAAAAAAAAADAIAAABKUGttbkZvdW5kYXRpb25zLldlYiwgVmVyc2lvbj0xLjAuMC4wLCBDdWx0dXJlPW5ldXRyYWwsIFB1YmxpY0tleVRva2VuPW51bGwFAQAAADZQa21uRm91bmRhdGlvbnMuV2ViLkhlYWRlckNvbG91citIZWFkZXJDb2xvdXJWaWV3U3RhdGUCAAAAEENvbnRyb2xWaWV3U3RhdGUIQ3NzQ2xhc3MCAQIAAAAKBgMAAAAEaG9tZQtkAgIPZBYCAgEPMswBAAEAAAD%2F%2F%2F%2F%2FAQAAAAAAAAAMAgAAAEpQa21uRm91bmRhdGlvbnMuV2ViLCBWZXJzaW9uPTEuMC4wLjAsIEN1bHR1cmU9bmV1dHJhbCwgUHVibGljS2V5VG9rZW49bnVsbAUBAAAANlBrbW5Gb3VuZGF0aW9ucy5XZWIuSGVhZGVyQ29sb3VyK0hlYWRlckNvbG91clZpZXdTdGF0ZQIAAAAQQ29udHJvbFZpZXdTdGF0ZQhDc3NDbGFzcwIBAgAAAAoGAwAAAANndHMLZAIBDxYCHgRUZXh0BQNndHNkAgIPZBYCZg8ytAYAAQAAAP%2F%2F%2F%2F8BAAAAAAAAAAwCAAAASlBrbW5Gb3VuZGF0aW9ucy5XZWIsIFZlcnNpb249MS4wLjAuMCwgQ3VsdHVyZT1uZXV0cmFsLCBQdWJsaWNLZXlUb2tlbj1udWxsBQEAAAA0UGttbkZvdW5kYXRpb25zLldlYi5SZXRpbmFJbWFnZStSZXRpbmFJbWFnZVZpZXdTdGF0ZQMAAAAYUmV0aW5hSW1hZ2VCYXNlVmlld1N0YXRlDEZvcm1hdFN0cmluZw5BbHRlcm5hdGVTaXplcwIBAQIAAAAJAwAAAAYEAAAAFHszfXswfUB7MjojLiMjfXguezF9BgUAAAADMiwzDAYAAABNU3lzdGVtLldlYiwgVmVyc2lvbj00LjAuMC4wLCBDdWx0dXJlPW5ldXRyYWwsIFB1YmxpY0tleVRva2VuPWIwM2Y1ZjdmMTFkNTBhM2EFAwAAADxQa21uRm91bmRhdGlvbnMuV2ViLlJldGluYUltYWdlQmFzZStSZXRpbmFJbWFnZUJhc2VWaWV3U3RhdGUEAAAADkltYWdlVmlld1N0YXRlCEltYWdlVXJsCENzc0NsYXNzC0tlZXBIaWdoUmVzAgEBAAECAAAACQcAAAAGCAAAABl%2BL2ltYWdlcy9oZWFkaW5nLWljb24ucG5nBgkAAAAMZ3RzTG9nb0ltYWdlAAUHAAAAElN5c3RlbS5XZWIuVUkuUGFpcgIAAAAFRmlyc3QGU2Vjb25kAgIGAAAACQoAAAAKBAoAAAAcU3lzdGVtLkNvbGxlY3Rpb25zLkFycmF5TGlzdAMAAAAGX2l0ZW1zBV9zaXplCF92ZXJzaW9uBQAACAgJCwAAAAYAAAAGAAAAEAsAAAAIAAAACQwAAAAGDQAAABNyZXRpbmEgZ3RzTG9nb0ltYWdlCQ4AAAAJCAAAAAkQAAAACAgCAAAADQIFDAAAABtTeXN0ZW0uV2ViLlVJLkluZGV4ZWRTdHJpbmcBAAAABl92YWx1ZQEGAAAABhEAAAAIQ3NzQ2xhc3MBDgAAAAwAAAAGEgAAAAhJbWFnZVVybAEQAAAADAAAAAYTAAAABF8hU0ILZAIDDxYCHwAFAjI2ZAIEDxYCHwAFBDk0MTVkAgwPZBYGZg9kFgYCAQ9kFgJmDzLEAgABAAAA%2F%2F%2F%2F%2FwEAAAAAAAAADAIAAABKUGttbkZvdW5kYXRpb25zLldlYiwgVmVyc2lvbj0xLjAuMC4wLCBDdWx0dXJlPW5ldXRyYWwsIFB1YmxpY0tleVRva2VuPW51bGwFAQAAAEFQa21uRm91bmRhdGlvbnMuV2ViLmNvbnRyb2xzLkZvcmVpZ25Mb29rdXArRm9yZWlnbkxvb2t1cFZpZXdTdGF0ZQUAAAAUVXNlckNvbnRyb2xWaWV3U3RhdGUJU291cmNlVXJsB01heFJvd3MUT25DbGllbnRWYWx1ZUNoYW5nZWQIQ3NzQ2xhc3MCAQABAQgCAAAACgYDAAAAJ34vY29udHJvbHMvUG9rZW1vblNvdXJjZS5hc2h4P2xpbWl0PTY0OQgAAAAKBgQAAAAPcGZQb2tlbW9uUGlja2VyCxYCAgYPFgYeBWNsYXNzBRhwZkxvb2t1cCBwZlBva2Vtb25QaWNrZXIeB29uZm9jdXMFsAFwZkhhbmRsZUxvb2t1cEtleXByZXNzKCdjcE1haW5fcHBTcGVjaWVzX3RoZUxvb2t1cCcsICdjcE1haW5fcHBTcGVjaWVzX3RoZUxvb2t1cF90eHRJbnB1dCcsICdjcE1haW5fcHBTcGVjaWVzX3RoZUxvb2t1cF9yZXN1bHRzJywgJzgnLCAnL2NvbnRyb2xzL1Bva2Vtb25Tb3VyY2UuYXNoeD9saW1pdD02NDknKR4Gb25ibHVyBTlwZkhpZGVMb29rdXBSZXN1bHRzKCdjcE1haW5fcHBTcGVjaWVzX3RoZUxvb2t1cF9yZXN1bHRzJykWBgIBDw9kFgofAgWwAXBmSGFuZGxlTG9va3VwS2V5cHJlc3MoJ2NwTWFpbl9wcFNwZWNpZXNfdGhlTG9va3VwJywgJ2NwTWFpbl9wcFNwZWNpZXNfdGhlTG9va3VwX3R4dElucHV0JywgJ2NwTWFpbl9wcFNwZWNpZXNfdGhlTG9va3VwX3Jlc3VsdHMnLCAnOCcsICcvY29udHJvbHMvUG9rZW1vblNvdXJjZS5hc2h4P2xpbWl0PTY0OScpHgpvbmtleXByZXNzBb8BcmV0dXJuIHBmSGFuZGxlTG9va3VwS2V5cHJlc3MyKCdjcE1haW5fcHBTcGVjaWVzX3RoZUxvb2t1cCcsICdjcE1haW5fcHBTcGVjaWVzX3RoZUxvb2t1cF90eHRJbnB1dCcsICdjcE1haW5fcHBTcGVjaWVzX3RoZUxvb2t1cF9yZXN1bHRzJywgJzgnLCAnL2NvbnRyb2xzL1Bva2Vtb25Tb3VyY2UuYXNoeD9saW1pdD02NDknLCBldmVudCkeB29ua2V5dXAFuAFyZXR1cm4gcGZIYW5kbGVMb29rdXBLZXlwcmVzczMoJ2NwTWFpbl9wcFNwZWNpZXNfdGhlTG9va3VwJywgJ2NwTWFpbl9wcFNwZWNpZXNfdGhlTG9va3VwX3R4dElucHV0JywgJ2NwTWFpbl9wcFNwZWNpZXNfdGhlTG9va3VwX3Jlc3VsdHMnLCAnOCcsICcvY29udHJvbHMvUG9rZW1vblNvdXJjZS5hc2h4P2xpbWl0PTY0OScpHghvbmNoYW5nZQWwAXBmSGFuZGxlTG9va3VwS2V5cHJlc3MoJ2NwTWFpbl9wcFNwZWNpZXNfdGhlTG9va3VwJywgJ2NwTWFpbl9wcFNwZWNpZXNfdGhlTG9va3VwX3R4dElucHV0JywgJ2NwTWFpbl9wcFNwZWNpZXNfdGhlTG9va3VwX3Jlc3VsdHMnLCAnOCcsICcvY29udHJvbHMvUG9rZW1vblNvdXJjZS5hc2h4P2xpbWl0PTY0OScpHwMFVXNldFRpbWVvdXQoZnVuY3Rpb24oKXtwZkhpZGVMb29rdXBSZXN1bHRzKCdjcE1haW5fcHBTcGVjaWVzX3RoZUxvb2t1cF9yZXN1bHRzJyl9LDEwMClkAgMPZBYCAgEPMpQGAAEAAAD%2F%2F%2F%2F%2FAQAAAAAAAAAMAgAAAEpQa21uRm91bmRhdGlvbnMuV2ViLCBWZXJzaW9uPTEuMC4wLjAsIEN1bHR1cmU9bmV1dHJhbCwgUHVibGljS2V5VG9rZW49bnVsbAUBAAAANFBrbW5Gb3VuZGF0aW9ucy5XZWIuUmV0aW5hSW1hZ2UrUmV0aW5hSW1hZ2VWaWV3U3RhdGUDAAAAGFJldGluYUltYWdlQmFzZVZpZXdTdGF0ZQxGb3JtYXRTdHJpbmcOQWx0ZXJuYXRlU2l6ZXMCAQECAAAACQMAAAAGBAAAABR7M317MH1AezI6Iy4jI314LnsxfQYFAAAAATIMBgAAAE1TeXN0ZW0uV2ViLCBWZXJzaW9uPTQuMC4wLjAsIEN1bHR1cmU9bmV1dHJhbCwgUHVibGljS2V5VG9rZW49YjAzZjVmN2YxMWQ1MGEzYQUDAAAAPFBrbW5Gb3VuZGF0aW9ucy5XZWIuUmV0aW5hSW1hZ2VCYXNlK1JldGluYUltYWdlQmFzZVZpZXdTdGF0ZQQAAAAOSW1hZ2VWaWV3U3RhdGUISW1hZ2VVcmwIQ3NzQ2xhc3MLS2VlcEhpZ2hSZXMCAQEAAQIAAAAJBwAAAAYIAAAAFH4vaW1hZ2VzL3dvcmtpbmcuZ2lmBgkAAAAAAAUHAAAAElN5c3RlbS5XZWIuVUkuUGFpcgIAAAAFRmlyc3QGU2Vjb25kAgIGAAAACQoAAAAKBAoAAAAcU3lzdGVtLkNvbGxlY3Rpb25zLkFycmF5TGlzdAMAAAAGX2l0ZW1zBV9zaXplCF92ZXJzaW9uBQAACAgJCwAAAAYAAAAGAAAAEAsAAAAIAAAACQwAAAAGDQAAAAZyZXRpbmEJDgAAAAkIAAAACRAAAAAICAIAAAANAgUMAAAAG1N5c3RlbS5XZWIuVUkuSW5kZXhlZFN0cmluZwEAAAAGX3ZhbHVlAQYAAAAGEQAAAAhDc3NDbGFzcwEOAAAADAAAAAYSAAAACEltYWdlVXJsARAAAAAMAAAABhMAAAAEXyFTQgtkAgUPFgIfBmRkAg0PD2QWAh8GBTdjaGFuZ2VkTWluKCdjcE1haW5fdHh0TGV2ZWxNaW4nLCAnY3BNYWluX3R4dExldmVsTWF4Jyk7ZAIPDw9kFgIfBgU3Y2hhbmdlZE1heCgnY3BNYWluX3R4dExldmVsTWluJywgJ2NwTWFpbl90eHRMZXZlbE1heCcpO2QCAQ8WAh4HVmlzaWJsZWhkAgIPFgIeC18hSXRlbUNvdW50AgcWDmYPZBYCZg8VEGY8aW1nIHNyYz0iL2ltYWdlcy9wa21uLWxnLzQxMC5wbmciIGFsdD0iU2hpZWxkb24iIGNsYXNzPSJzcHJpdGUgc3BlY2llcyIgd2lkdGg9Ijk2cHgiIGhlaWdodD0iOTZweCIgLz6BATxpbWcgc3JjPSIvaW1hZ2VzL2l0ZW0tc20vMzAwNC5wbmciIGFsdD0iUG9rJiMyMzM7IEJhbGwiIHRpdGxlPSJQb2smIzIzMzsgQmFsbCIgY2xhc3M9InNwcml0ZSBpdGVtIiB3aWR0aD0iMjRweCIgaGVpZ2h0PSIyNHB4IiAvPgIyMAPimYIABVRSVU1QB0Vsa2V2aW4IU2hpZWxkb24DNDEwAHI8aW1nIHNyYz0iL2ltYWdlcy9wa21uLXNtLzE3Ny5wbmciIGFsdD0iTmF0dSIgY2xhc3M9InNwcml0ZSBzcGVjaWVzU21hbGwiIHdpZHRoPSI0MHB4IiBoZWlnaHQ9IjMycHgiIC8%2BTmF0dSAoIzE3NykHUmVsYXhlZAAJQW55IGxldmVsBlN0dXJkeSNUaHVyc2RheSwgTm92ZW1iZXIgMjQsIDIwMTYgNjowOSBBTWQCAQ9kFgJmDxUQZDxpbWcgc3JjPSIvaW1hZ2VzL3BrbW4tbGcvMjU4LnBuZyIgYWx0PSJNdWRraXAiIGNsYXNzPSJzcHJpdGUgc3BlY2llcyIgd2lkdGg9Ijk2cHgiIGhlaWdodD0iOTZweCIgLz6BATxpbWcgc3JjPSIvaW1hZ2VzL2l0ZW0tc20vMzAwNC5wbmciIGFsdD0iUG9rJiMyMzM7IEJhbGwiIHRpdGxlPSJQb2smIzIzMzsgQmFsbCIgY2xhc3M9InNwcml0ZSBpdGVtIiB3aWR0aD0iMjRweCIgaGVpZ2h0PSIyNHB4IiAvPgE1A%2BKZgAAGTVVES0lQBkhhWm5hawZNdWRraXADMjU4AHo8aW1nIHNyYz0iL2ltYWdlcy9wa21uLXNtLzM5MC5wbmciIGFsdD0iQ2hpbWNoYXIiIGNsYXNzPSJzcHJpdGUgc3BlY2llc1NtYWxsIiB3aWR0aD0iNDBweCIgaGVpZ2h0PSIzMnB4IiAvPkNoaW1jaGFyICgjMzkwKQRDYWxtAAlBbnkgbGV2ZWwHVG9ycmVudCNUaHVyc2RheSwgTm92ZW1iZXIgMjQsIDIwMTYgMzoxMiBBTWQCAg9kFgJmDxUQZTxpbWcgc3JjPSIvaW1hZ2VzL3BrbW4tbGcvNzUucG5nIiBhbHQ9IkdyYXZlbGVyIiBjbGFzcz0ic3ByaXRlIHNwZWNpZXMiIHdpZHRoPSI5NnB4IiBoZWlnaHQ9Ijk2cHgiIC8%2BeTxpbWcgc3JjPSIvaW1hZ2VzL2l0ZW0tc20vMzAwMy5wbmciIGFsdD0iR3JlYXQgQmFsbCIgdGl0bGU9IkdyZWF0IEJhbGwiIGNsYXNzPSJzcHJpdGUgaXRlbSIgd2lkdGg9IjI0cHgiIGhlaWdodD0iMjRweCIgLz4CMjMD4pmCAAhHUkFWRUxFUgRMaW5rCEdyYXZlbGVyAjc1AHg8aW1nIHNyYz0iL2ltYWdlcy9wa21uLXNtLzc1LnBuZyIgYWx0PSJHcmF2ZWxlciIgY2xhc3M9InNwcml0ZSBzcGVjaWVzU21hbGwiIHdpZHRoPSI0MHB4IiBoZWlnaHQ9IjMycHgiIC8%2BR3JhdmVsZXIgKCM3NSkHQmFzaGZ1bAAJQW55IGxldmVsCVJvY2sgSGVhZCRXZWRuZXNkYXksIE5vdmVtYmVyIDIzLCAyMDE2IDQ6MzcgUE1kAgMPZBYCZg8VEGM8aW1nIHNyYz0iL2ltYWdlcy9wa21uLWxnLzI4MC5wbmciIGFsdD0iUmFsdHMiIGNsYXNzPSJzcHJpdGUgc3BlY2llcyIgd2lkdGg9Ijk2cHgiIGhlaWdodD0iOTZweCIgLz55PGltZyBzcmM9Ii9pbWFnZXMvaXRlbS1zbS8zMDAyLnBuZyIgYWx0PSJVbHRyYSBCYWxsIiB0aXRsZT0iVWx0cmEgQmFsbCIgY2xhc3M9InNwcml0ZSBpdGVtIiB3aWR0aD0iMjRweCIgaGVpZ2h0PSIyNHB4IiAvPgIxMQPimYIABVJBTFRTDE5pY29sJiMyMjU7cwVSYWx0cwMyODAAeDxpbWcgc3JjPSIvaW1hZ2VzL3BrbW4tc20vMS5wbmciIGFsdD0iQnVsYmFzYXVyIiBjbGFzcz0ic3ByaXRlIHNwZWNpZXNTbWFsbCIgd2lkdGg9IjQwcHgiIGhlaWdodD0iMzJweCIgLz5CdWxiYXNhdXIgKCMxKQZHZW50bGUACUFueSBsZXZlbAtTeW5jaHJvbml6ZSFNb25kYXksIE5vdmVtYmVyIDIxLCAyMDE2IDk6MjEgUE1kAgQPZBYCZg8VEGY8aW1nIHNyYz0iL2ltYWdlcy9wa21uLWxnLzEyMi5wbmciIGFsdD0iTXIuIE1pbWUiIGNsYXNzPSJzcHJpdGUgc3BlY2llcyIgd2lkdGg9Ijk2cHgiIGhlaWdodD0iOTZweCIgLz55PGltZyBzcmM9Ii9pbWFnZXMvaXRlbS1zbS8zMDAyLnBuZyIgYWx0PSJVbHRyYSBCYWxsIiB0aXRsZT0iVWx0cmEgQmFsbCIgY2xhc3M9InNwcml0ZSBpdGVtIiB3aWR0aD0iMjRweCIgaGVpZ2h0PSIyNHB4IiAvPgIzMwPimYIACE1SLiBNSU1FB1Byb2xvbmcITXIuIE1pbWUDMTIyAHg8aW1nIHNyYz0iL2ltYWdlcy9wa21uLXNtLzQ1Ni5wbmciIGFsdD0iRmlubmVvbiIgY2xhc3M9InNwcml0ZSBzcGVjaWVzU21hbGwiIHdpZHRoPSI0MHB4IiBoZWlnaHQ9IjMycHgiIC8%2BRmlubmVvbiAoIzQ1NikDTGF4A%2BKZgglBbnkgbGV2ZWwKU291bmRwcm9vZiFNb25kYXksIE5vdmVtYmVyIDIxLCAyMDE2IDY6MDUgUE1kAgUPZBYCZg8VEGA8aW1nIHNyYz0iL2ltYWdlcy9wa21uLWxnLzg5LnBuZyIgYWx0PSJNdWsiIGNsYXNzPSJzcHJpdGUgc3BlY2llcyIgd2lkdGg9Ijk2cHgiIGhlaWdodD0iOTZweCIgLz55PGltZyBzcmM9Ii9pbWFnZXMvaXRlbS1zbS8zMDAyLnBuZyIgYWx0PSJVbHRyYSBCYWxsIiB0aXRsZT0iVWx0cmEgQmFsbCIgY2xhc3M9InNwcml0ZSBpdGVtIiB3aWR0aD0iMjRweCIgaGVpZ2h0PSIyNHB4IiAvPgI1OAPimYIAA01VSwNPUk8DTXVrAjg5AHQ8aW1nIHNyYz0iL2ltYWdlcy9wa21uLXNtLzM3LnBuZyIgYWx0PSJWdWxwaXgiIGNsYXNzPSJzcHJpdGUgc3BlY2llc1NtYWxsIiB3aWR0aD0iNDBweCIgaGVpZ2h0PSIzMnB4IiAvPlZ1bHBpeCAoIzM3KQNMYXgADkx2LiAxMDAgdG8gMTAwBlN0ZW5jaCJTdW5kYXksIE5vdmVtYmVyIDIwLCAyMDE2IDExOjAxIFBNZAIGD2QWAmYPFRBkPGltZyBzcmM9Ii9pbWFnZXMvcGttbi1sZy8xOC5wbmciIGFsdD0iUGlkZ2VvdCIgY2xhc3M9InNwcml0ZSBzcGVjaWVzIiB3aWR0aD0iOTZweCIgaGVpZ2h0PSI5NnB4IiAvPgADMTAwA%2BKZggAE672EPw%2FvvKPvvKjvvKHvvK%2FimaEHUGlkZ2VvdAIxOHA8aW1nIHNyYz0iL2ltYWdlcy9pdGVtLXNtLzMyMTAucG5nIiBhbHQ9IlNoYXJwIEJlYWsiIGNsYXNzPSJzcHJpdGUgaXRlbSIgd2lkdGg9IjI0cHgiIGhlaWdodD0iMjRweCIgLz5TaGFycCBCZWFrejxpbWcgc3JjPSIvaW1hZ2VzL3BrbW4tc20vMzgucG5nIiBhbHQ9Ik5pbmV0YWxlcyIgY2xhc3M9InNwcml0ZSBzcGVjaWVzU21hbGwiIHdpZHRoPSI0MHB4IiBoZWlnaHQ9IjMycHgiIC8%2BTmluZXRhbGVzICgjMzgpBUpvbGx5A%2BKZgglBbnkgbGV2ZWwIS2VlbiBFeWUiVHVlc2RheSwgTm92ZW1iZXIgMTUsIDIwMTYgMjo1OSBQTWQYAQUeX19Db250cm9sc1JlcXVpcmVQb3N0QmFja0tleV9fFgUFE2N0bDAwJGNwTWFpbiRyYkdlbjQFE2N0bDAwJGNwTWFpbiRyYkdlbjUFE2N0bDAwJGNwTWFpbiRyYkdlbjUFFGN0bDAwJGNwTWFpbiRjaGtNYWxlBRZjdGwwMCRjcE1haW4kY2hrRmVtYWxlCtDi1w0%2BVpGjeGU2sfG4PJ2re0bMhLcEzHfi5C83SQ0%3D&__VIEWSTATEGENERATOR=DCBAB175&__EVENTVALIDATION=%2FwEdAAr2N0PRcOIgE%2BioK05h%2B08KilTywxVrGV9TGO7VWCucxILxjij27BpTHsj%2FSE2Em64VTLR8rO4SFh0o3kWgKWSdMEGhWov60gdVPx5kFpEAQQBuMbIM7jLheA9PPjiCGOWbKuueirXZ%2BP3t6JNCPBrKltxqlOtJEyGk4jQGfPmSH8grted3t6RWrwXlhYXFxckEoD%2Fhp3wRhdteT59NoV2x2cjVXs5kTEfgzHaI2g276F2wuJnmlr8RGpusUWuFseg%3D&ctl00%24cpMain%24ppSpecies%24theLookup%24txtInput=&ctl00%24cpMain%24ppSpecies%24theLookup%24hdSelectedValue=&ctl00%24cpMain%24btnSearch=Search&ctl00%24cpMain%24txtLevelMin=1&ctl00%24cpMain%24txtLevelMax=100&ctl00%24cpMain%24chkMale=on&ctl00%24cpMain%24chkFemale=on&'.
            http_build_query($post)
        );
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return curl_exec($ch);
    }
}
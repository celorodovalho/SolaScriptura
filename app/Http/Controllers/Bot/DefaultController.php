<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use App\Providers\Dbt;
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

    /**
     * @param Request $request
     * DAM ID – the unique 10-character id by which an individual volume is identified.
    1-3: Language code, e.g., ENG
    4-6: Version code, e.g., ESV
    7: Collection
    "O" Old Testament – Contains one or more books of the Old Testament.
    "N" New Testament – Contains one or more books of the New Testament.
    "C" Complete – Contains books from both the Old and New Testament. (These volumes are used primarily for content downloads, and are not generally used by the actual reader applications).
    "S" Story – Contains content that is not organized by books and chapters.
    "P" Partial – Contains only partial content, such as a few chapters from one book of the Bible.
    8: Drama type
    "1" (Audio includes only spoken text)
    "2" (Audio includes spoken text, music, and sound effects)
    9-10: Media type
    ET – Electronic Text
    DA – Digital Audio
    DV – Digital Video
    Examples for the English KJV:
    ENGKJVC1DA – Complete (for download) non-drama audio
    ENGKJVC2DA – Complete (for download) drama audio
    ENGKJVO1DA – Old Testament non-drama audio
    ENGKJVO1ET – Old Testament non-drama text
    ENGKJVO2DA – Old Testament drama audio
    ENGKJVO2ET – Old Testament drama text
    ENGKJVN1DA – New Testament non-drama audio
    ENGKJVN1ET – New Testament non-drama text
    ENGKJVN2DA – New Testament drama audio
    ENGKJVN2ET – New Testament drama text
     */
    public function test(Request $request)
    {

//        dump($dbt->getLibraryVerseinfo('ENGKJVC1DA', 'Gen', '1', '1', '2')); //getVerseStart($damId, $bookId, $chapterId)

//        dump(654);die;
//        $book = \App\Verses::find(1)->books()->where('abbrev', 'gn')->first(); //
//        App\Post::find(1)->comments()->where('title', 'foo')->first();
//        dump($book);
        die;
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\SignatureValidator;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Illuminate\Http\Request; 
use Curl, Log;

class LineBotController extends Controller
{
    public function index(Request $request)
    {
    	echo 'hi';
    }

    public function message(Request $request)
    {
        $lineAccessToken = env('LINE_BOT_CHANNEL_ACCESS_TOKEN');
        $lineChannelSecret = env('LINE_BOT_CHANNEL_SECRET');

        $signature = $request->headers->get(HTTPHeader::LINE_SIGNATURE);

        if ($signature == '') {
        	return;
        }

        if (!SignatureValidator::validateSignature($request->getContent(), $lineChannelSecret, $signature)) {
            return;
        }

        $httpClient = new CurlHTTPClient ($lineAccessToken);
        $lineBot = new LINEBot($httpClient, ['channelSecret' => $lineChannelSecret]);

        try {
            $events = $lineBot->parseEventRequest($request->getContent(), $signature);

            foreach ($events as $event) {
                $getText = '';
                $userId = $event->getUserId();
                $replyToken = $event->getReplyToken();
                $messageType = $event->getMessageType();

                //訊息的話
                if ($event instanceof MessageEvent) {
                    //文字
                    if ($messageType == 'text') {
                        $text = $event->getText();// 得到使用者輸入
                        $getText = $text;
                        $lineBot->replyText($replyToken, $text);// 回復使用者輸入
                    }
                }

                //Log
                $log = [
                    'userId' => $userId,
                    'text' => $getText,
                    'type' => $messageType,
                ];
                
                Log::info(json_encode($log));
            }
        } catch (Exception $e) {
            return;
        }
        return;
    }
}
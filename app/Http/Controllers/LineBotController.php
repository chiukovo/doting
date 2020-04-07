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

    public function __construct()
    {
        $lineAccessToken = env('LINE_BOT_CHANNEL_ACCESS_TOKEN');
        $lineChannelSecret = env('LINE_BOT_CHANNEL_SECRET');

        $httpClient = new CurlHTTPClient ($lineAccessToken);
        $this->lineBot = new LINEBot($httpClient, ['channelSecret' => $lineChannelSecret]);
    }

    public function index(Request $request)
    {
    	echo 'hi';
    }

    public function search(Request $request)
    {
        return view('search');
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

        try {
            $events = $this->lineBot->parseEventRequest($request->getContent(), $signature);

            foreach ($events as $event) {
                $text = '';
                $userId = $event->getUserId();
                $replyToken = $event->getReplyToken();
                $messageType = $event->getMessageType();

                //訊息的話
                if ($event instanceof MessageEvent) {
                    //文字
                    if ($messageType == 'text') {
                        $text = $event->getText();// 得到使用者輸入
                        $this->lineBot->replyText($replyToken, $text);// 回復使用者輸入
                    }
                }

                //Log
                $log = [
                    'userId' => $userId,
                    'text' => $text,
                    'type' => $messageType,
                ];
                
                Log::info(json_encode($log));
            }
        } catch (Exception $e) {
            return;
        }
        return;
    }

    public function getApiByText($text)
    {
    }

    public function sendMsg($msg)
    {
        $textMessageBuilder = new TextMessageBuilder($msg);
        $this->lineBot->pushMessage('id', $textMessageBuilder);
    }
}
<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class Telegram
{

    public $http, $url;

    public function __construct()
    {
        $this->http = new Http();
        $this->url = "https://api.telegram.org/bot" . config('bot.telegram.token') . "/";
    }


    public function sendMessage($message, $chat_id)
    {
        $result = $this->http::connectTimeout(2)->get($this->url.'sendMessage', [
                'chat_id' =>$chat_id,
                'text' => $message,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ]);
        return $result->body();
    }



}

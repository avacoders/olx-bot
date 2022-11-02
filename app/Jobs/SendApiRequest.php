<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendApiRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $http, $url, $data, $chat_id;

    public function __construct($data)
    {
        $this->http = new Http();
        $this->data = $data;
        $this->url = "https://api.telegram.org/bot" . config('bot.telegram.token') . "/";
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $result = $this->http::get($this->url.'sendMessage', [
            'chat_id' =>config("bot.telegram.chat_id"),
            'text' => $this->data,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ]);
    }
}

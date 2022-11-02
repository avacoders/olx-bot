<?php

namespace App\Http\Services;

use App\Models\Elon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OlxService
{
    public $telegram, $http;


    public function __construct()
    {
        $this->telegram = new Telegram();
        $this->http = new Http();
    }


    public function getAllData()
    {
        $result = $this->http::get("https://www.olx.uz/api/v1/offers/", [
            'category_id' => 1147,
            'city_id' => 4,
            'limit' => 50,
            'currency' => 'USD',
            'region_id' => 5,
            'offset' => 0,
            'filter_float_price:to' => 400,
        ]);
        $data = json_decode($result->body(), true)['data'];
        foreach ($data as $item) {
//            dd($item);
            $this->send($item);
        }

    }


    public function send($data)
    {
        $date = Carbon::parse($data['last_refresh_time'])->format('d M Y, H:i');
        $text = "Опубликовано $date";
        $text .= "\n<a href='" . $data['url'] . "'><b>" . str_replace('<br', "\n", $data['title']) . "</b></a>";
        $text .= "\nЦена: " . $this->getParam($data['params'], 'price')['label'];
        $text .= "\nКоличество комнат: " . $this->getParam($data['params'], 'number_of_rooms')['label'];
        $text .= "\nОбщая площадь: " . $this->getParam($data['params'], 'total_area')['label'];
        $text .= "\nПолзователь: " . $data['user']['name'];
        $text .= "\nОПИСАНИЕ:\n" . str_replace('<br', "\n", $data['description']);
        if (!$data['promotion']['top_ad'] && !Elon::find($data['id']))
        {
            Elon::create(['id' => $data['id']]);
            $response = json_decode($this->telegram->sendMessage($text, config('bot.telegram.chat_id')), true);
            if(!$response['ok'])
            {
                Log::debug($response);
            }

        }
//            dd($text);
//        return $text;
    }

    public function getParam($array, $key)
    {
        foreach ($array as $item) {
            if ($item['key'] == $key) {
                return $item['value'];
            }
        }
        return null;
    }


}

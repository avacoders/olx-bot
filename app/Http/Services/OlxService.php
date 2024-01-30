<?php

namespace App\Http\Services;

use App\Jobs\SendApiRequest;
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
            'district_id' => 25,
            'offset' => 0,
            'filter_float_price:from' => 100,
            'filter_float_price:to' => 400,
            "filter_enum_comission[0]" => "no",
        ]);
        $data = json_decode($result->body(), true)['data'];
        $rateLimitPerMinute = 60;
        $counter = 1;
        $insert = [];
        foreach ($data as $item) {
            if (!$item['promotion']['top_ad'] && !Elon::find($item['id'])) {
                $insert[] = ['id' => $item['id'], 'created_at' => Carbon::now()];
                $delayInMinutes = intval($counter / $rateLimitPerMinute);
                SendApiRequest::dispatch($this->text($item))->delay(now()->addMinutes($delayInMinutes));
                $counter++;
            }

        }
        Elon::insert($insert);

    }


    public function text($data)
    {
        $date = Carbon::parse($data['last_refresh_time'])->format('d M Y, H:i');
        $text = "Опубликовано $date";
        $text .= "\n<a href='" . $data['url'] . "'><b>" . str_replace('<br', "\n", str_replace('/>', "", $data['title'])) . "</b></a>";
        $text .= "\nЦена: " . $this->getParam($data['params'], 'price')['label'];
        $text .= "\nКоличество комнат: " . $this->getParam($data['params'], 'number_of_rooms')['label'];
        $text .= "\nОбщая площадь: " . $this->getParam($data['params'], 'total_area')['label'];
        $text .= "\nПолзователь: " . $data['user']['name'];
        $text .= "\nОПИСАНИЕ: " . str_replace('<br', "\n", str_replace('/>', "", $data['description']));
        return $text;
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

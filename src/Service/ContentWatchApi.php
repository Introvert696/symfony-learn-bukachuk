<?php

namespace App\Service;

class ContentWatchApi
{
    public function checkText(string $text) : int
    {
        $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_POSTFIELDS, [
                'key' => 'SuCRvNMdDarbLcy',
                'text' => $text,
                'test' => 0
            ]);
            curl_setopt($curl, CURLOPT_URL, 'https://content-watch.ru/public/api/');
            $data = json_decode(trim(curl_exec($curl)),true);
            curl_close($curl);

            return (int)($data['percent']);
}
}
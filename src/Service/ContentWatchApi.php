<?php

namespace App\Service;

class ContentWatchApi
{
    public function __construct(
        private readonly string $key,
        $translator
//        Тут мы прописываем то что хотим что то получить при помощи autowire, но не указали конкретно что
    )
    {
        dd($translator);
    }
    public function checkText(string $text) : int
    {
        $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_POSTFIELDS, [
                'key' => $this->key,
                'text' => $text,
                'test' => 0
            ]);
            curl_setopt($curl, CURLOPT_URL, 'https://content-watch.ru/public/api/');
            $data = json_decode(trim(curl_exec($curl)),true);
            curl_close($curl);

            return (int)($data['percent']);
}
}
<?php


// текст на проверку
$text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce sit amet mollis nunc. Duis sodales, risus ac semper gravida, urna purus varius sapien, eget ultricies felis sapien ac nunc. Aliquam id viverra velit. Curabitur quis mauris et arcu sodales convallis. Praesent ut mauris ac massa scelerisque hendrerit sed id arcu. In hac habitasse platea dictumst. Phasellus ultrices ligula diam, et iaculis odio fringilla nec. Suspendisse posuere sapien mauris, at pretium justo malesuada eu. Quisque justo libero, tincidunt vel quam eget, condimentum condimentum justo. Fusce semper enim leo, in efficitur enim sollicitudin eu.';


$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_POST, TRUE);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_POSTFIELDS, [
    'key' => 'SuCRvNMdDarbLcy',
    'text' => $text,
    'test' => 0
]);
curl_setopt($curl, CURLOPT_URL, 'https://content-watch.ru/public/api/');
if(!$result = curl_exec($curl)){
    var_dump(curl_error($curl));
}
$res = curl_exec($curl);
curl_close($curl);
//var_dump($res);
$return = json_decode(trim(curl_exec($curl)), TRUE);
var_dump($return);
exit;




// если в ответе нет переменной error, значит запрос не удался
if (!isset($return['error'])) {
    echo 'Ошибка запроса';

// если переменная error не пустая, значит при проверке возникла ошибка
} else if (!empty($return['error'])) {
    echo 'Возникла ошибка: ' . $return['error'];

// парсим ответ
} else {
    // инициализируем дефолтные значения
    $defaults = array(
        'text' => '',
        'percent' => '100.0',
        'highlight' => array(),
        'matches' => array()
    );
    $return = array_merge($defaults, $return);

    // выводим в невидимое поле чистый текст, который будем использовать как основу для подсветки совпадений
    echo '
        <div id="clean_text" style="display: none;">' . $return['text'] . '</div>';

    // выводим поле для текста с подсветкой совпадений
    echo '
        <div id="hl_text"></div>
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
        <script type="text/javascript">
        function highlight_words(hl_array)
        {
            var t_hl = $("#clean_text").text().split(" ");
            for (i = 0; i < hl_array.length; i++)
            {
                if (hl_array[i] instanceof Array) {
                    t_hl[ hl_array[i][0] ] = "<b>" + (t_hl[ hl_array[i][0] ] === undefined ? "" : t_hl[ hl_array[i][0] ]);
                    t_hl[ hl_array[i][1] ] = (t_hl[ hl_array[i][1] ] === undefined ? "" : t_hl[ hl_array[i][1] ]) + "</b>";
                } else {
                    t_hl[ hl_array[i] ] = "<b>" + t_hl[ hl_array[i] ] + "</b>";
                }
            }
            $("#hl_text").html(t_hl.join(" "));
            return false;
        }
        </script>';

    // при загрузке страницы подсвечиваем общие совпадения
    echo '
        <script type="text/javascript">
        $(document).ready(function()
        {
            highlight_words(' . json_encode($return['highlight']) . ');
        });
        </script>';

    // выводим результат проверки
    echo '
        <h1>Уникальность текста: ' . $return['percent'] . '</h1>';

    // выводим совпадения
    echo '
        <table border="0" cellpadding="5" cellspacing="0">';

    foreach ($return['matches'] as $match) {
        echo '
            <tr>
                <td><a href="' . $match['url'] . '" target="_blank">' . $match['url'] . '</a></td>
                <td><strong>' . $match['percent'] . '%</strong></td>
                <td><a href="#" onclick=\'return highlight_words('
            . json_encode($match['highlight'])
            . ');\'>подсветить совпадения</a></td>
            </tr>';
    }
    echo '
        </table>
        <p><a href="#" onclick=\'return highlight_words(' . json_encode($return['highlight']) . ');\'>Подсветить все совпадения</a></p>';
}

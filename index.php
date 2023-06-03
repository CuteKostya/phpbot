<?php

# Принимаем запрос
$data = json_decode(file_get_contents('php://input'), TRUE);
file_put_contents('file.txt', '$data: '.print_r($data, 1)."\n", FILE_APPEND);


//https://api.telegram.org/bot*Токен бота*/setwebhook?url=*ссылка на бота*


# Обрабатываем ручной ввод или нажатие на кнопку
$data = $data['callback_query'] ? $data['callback_query'] : $data['message'];



# Записываем сообщение пользователя
$message = mb_strtolower(($data['text'] ? $data['text'] : $data['data']),'utf-8');



$method = 'sendMessage';
$send_data = [
    'text'   => 'text'
];

# Добавляем данные пользователя
$send_data['chat_id'] = $data['chat']['id'];

$res = sendTelegram($method, $send_data);

function sendTelegram($method, $data, $headers = [])
{
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://api.telegram.org/bot' . TOKEN . '/' . $method,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array_merge(array("Content-Type: application/json"), $headers)
    ]);

    $result = curl_exec($curl);
    curl_close($curl);
    return (json_decode($result, 1) ? json_decode($result, 1) : $result);
}
function calculator($equation)
{
    $equation = preg_replace("/[^0-9+\-.*\/()%]/","",$equation);
    // fix percentage calcul when percentage value < 10
    $equation = preg_replace("/([+-])([0-9]{1})(%)/","*(1\$1.0\$2)",$equation);
    // calc percentage
    $equation = preg_replace("/([+-])([0-9]+)(%)/","*(1\$1.\$2)",$equation);

    if ( $equation == "" )
    {
        $return = 0;
    }
    else
    {
        @eval("\$return=" . $equation . ";" );
    }
    return $return;
}
function calculatorChar($text)
{
    $text = '(' . $text . ')';

    for($i = 0; $i < strlen($text); $i++){

        $text = abzads($text);

    }

    return $text;
}
function abzads($text){

    $predl = '';
    for($i = 0; $i < strlen($text); $i++){
        if($text[$i] == ')' ){
            for($j = $i - 1; true; $j--){

                if($text[$j] == '(' ){
                    break;
                }
                $predl = $text[$j] . $predl;

            }

            break;
        }
    }

    return str_replace('(' . $predl . ')', predloz($predl), $text);



}
function predloz($text){
    for($i = 0; $i < strlen($text); $i++){
        if($text[$i] == '*' || $text[$i] == '/'){
            $text = findAndReplacement($i, $text );
            $i = 0;

        }
    }
    for($i = 0; $i < strlen($text); $i++){
        if($text[$i] == '+' || $text[$i] == '-'){
            $text = findAndReplacement($i, $text );


            $i = 0;
        }

    }


    return $text;
}


function findAndReplacement($i, $text){
    $firstNum = '';
    $sign = '';
    $lastNum = '';

    $sign = $text[$i];

    for($j = $i - 1; true; $j--){
        if($j < 0 || $text[$j] == '+' || $text[$j] == '-' || $text[$j] == '*' || $text[$j] == '/'){
            break;
        }
        $firstNum = $text[$j] . $firstNum;
    }

    for($j = $i + 1; true; $j++){

        if($j + 1 > strlen($text))
            break;

        if($text[$j] == '+' || $text[$j] == '-' || $text[$j] == '*' || $text[$j] == '/')
            break;

        $lastNum =  $lastNum . $text[$j] ;

    }
    return str_replace($firstNum . $sign . $lastNum, oper( $firstNum, $sign, $lastNum ),$text);


}

function oper($firstNum, $sign, $lastNum)
{
    if($sign=="+")
    {
        $res= $firstNum+$lastNum;
    }
    if($sign=="-")
    {
        $res= $firstNum-$lastNum;
    }
    if($sign=="*")
    {
        $res =$firstNum*$lastNum;
    }
    if($sign=="/")
    {
        $res= $firstNum/$lastNum;
    }

    return $res;
}

<?php
namespace nikserg\cryptoprohttpclient;

use yii\base\Exception;

class CryptoproHttpClient {

    /**
     * @var string Путь к исполняемому файлу Curl КриптоПро
     */
    public static $curlExec = '/opt/cprocsp/bin/amd64/curl';


    public static function request($url, $requestBody = null, $certificateThumbprint = null, $headers = [])
    {
        $requestBody = str_replace("'", "'\"'\"'", $requestBody);

        $shellCommand = 'yes "o" | '. self::$curlExec .
            ' -k -s '. $url . ' '.
            '--header "Content-Type: text/xml; charset=\"UTF-8\"" ';

        //Заголовки
        foreach ($headers as $key => $value) {
            if (!is_array($value)) {
                $value = [$value];
            }
            foreach ($value as $valueItem) {
                $valueItem = str_replace('"', '\"', $valueItem);
                $shellCommand .= ' --header "'.$key.': '.$valueItem.'"';
            }
        }
        if ($certificateThumbprint) {
            $shellCommand .= ' --cert ' . $certificateThumbprint;
        }
        if ($requestBody) {
            $shellCommand .= ' --data \'' . $requestBody . '\'';
        }

        //die($shellCommand);

        $result = shell_exec($shellCommand);
        if (!$result) {
            throw new \Exception('Не получен ответ на команду ' . $shellCommand);
        }
        $xml = html_entity_decode($result, ENT_QUOTES | ENT_HTML5);

        //Ошибка SOAP
        /*preg_match('/<faultstring.*>(.*)<\/faultstring>/sUu', $xml, $errorMatches);
        if (isset($errorMatches[1]) && $errorMatches[1]) {
            preg_match('/<mserror:source>(.*)<\/mserror:source>/s', $xml, $sourceMatches);
            $source = null;
            if (isset($sourceMatches[1]) && $sourceMatches[1]) {
                $source = $sourceMatches[1];
            }
            throw new \Exception($url. $errorMatches[1]);
        }*/


        return $xml;
        //XML результата
        /*preg_match('/<' . $action . 'Result.*>(.*)<\/' . $action . 'Result>/sUu', $xml, $matches);
        if (!isset($matches[1]) || !$matches[1]) {
            //Запрос выполнен, пустой результат
            preg_match('/<q1:' . $action . 'Response xmlns:q1=/s', $xml, $emptyResponseMatches);
            if (isset($emptyResponseMatches[0]) && $emptyResponseMatches[0]) {
                return true;
            }

            throw new UCSoapTransportException('Получен не SOAP-ответ на команду ' . $shellCommand . ': ' . $result);
        }

        $resultXML = $matches[1];

        //die($resultXML);

        return $resultXML;*/
    }
}

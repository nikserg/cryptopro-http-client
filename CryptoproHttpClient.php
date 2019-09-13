<?php
namespace nikserg\cryptoprohttpclient;

class CryptoproHttpClient {

    /**
     * @var string Путь к исполняемому файлу Curl КриптоПро
     */
    public static $curlExec = '/opt/cprocsp/bin/amd64/curl';


    public static function request($url, $requestBody = null, $certificateThumbprint = null, $headers = [])
    {
        $requestBody = str_replace("'", "'\"'\"'", $requestBody);

        $shellCommand = self::$curlExec .
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

        $result = shell_exec($shellCommand);
        if (!$result) {
            throw new \Exception('Не получен ответ на команду ' . $shellCommand);
        }

        $xml = $result;

        return $xml;
    }
}

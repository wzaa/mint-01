<?php
namespace App\Helper;

class JsonFileHandler
{
    public function readJsonFile(string $filename)
    {
        $json = file_get_contents($filename);
        $content = json_decode($json, true);

        return $content;
    }

    public function writeJsonFile(string $filename, $data, bool $prettyPrint)
    {
        $json = json_encode($data, $prettyPrint ? JSON_PRETTY_PRINT : 0);

        file_put_contents($filename, $json);
    }
}

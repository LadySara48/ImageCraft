<?php

namespace hearlov\ImageCraft\asyncpool;

use hearlov\ImageCraft\MapProcess;
use pocketmine\scheduler\AsyncTask;

class DownloadTask extends AsyncTask{

    public function __construct(public string $data){}

    public function onRun(): void{
        $data = unserialize($this->data);

        $ch = curl_init($data["url"]);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_USERAGENT => "Mozilla/5.0 (ImageCraftFetcher)",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ]);
        $imageContent = curl_exec($ch);
        $error = curl_error($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($imageContent === false || $http !== 200){
            $this->setResult("The received data caused a mismatch. HTTP $http | $error");
            return;
        }

        if (!in_array(getimagesizefromstring($imageContent)["mime"] ?? "", ["image/jpeg", "image/png", "image/jpg", "image/webp"])) {
            $this->setResult("The received file is not a supported image format.");
            return;
        }

        $fullpath = $data["fullpath"] . "." . $data["extension"];

        if(file_exists($fullpath)){
            unlink($fullpath);
        }

        if(file_put_contents($fullpath, $imageContent) === false){
            $this->setResult("The received data could not be written to file.");
            return;
        }

        $this->setResult(true);
    }

    public function onCompletion(): void{
        $data = unserialize($this->data);
        if($this->getResult() !== true){
            MapProcess::commandRequestUncorfirmed($data["player"], $this->getResult());
            var_dump($this->getResult());
            return;
        }
        MapProcess::commandRequestConfirmed(
            $data["fullpath"], $data["name"], $data["extension"],
            $data["width"], $data["height"], $data["player"]
        );
    }

}
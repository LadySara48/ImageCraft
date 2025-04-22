<?php

namespace hearlov\ImageCraft\math;

use hearlov\ImageCraft\lib\gumlet\ImageResize;

class ImageSplitter{

    CONST PIXELS_PER_MAP = 0x80;

    public static function splitImage(string $file, int $width, int $height, string $extension = "png"): array{
        $nwidth = $width * self::PIXELS_PER_MAP;
        $nheight = $height * self::PIXELS_PER_MAP;
        $newname = "$file-resized.png";

        $image = new ImageResize("$file.$extension");
        $image->resize($nwidth, $nheight, true);
        $image->save("$newname", IMAGETYPE_PNG);

        $spimage = new ImageResize($newname);

        $cropped_items = [];
        $data = [];
        $id = 0;
        for($w = 0; $w < $width; ++$w){
            $swidth = $w * self::PIXELS_PER_MAP;
            for($h = 0; $h < $height; ++$h){
                $sheight = $h * self::PIXELS_PER_MAP;

                $spimage->freecrop(128, 128, $swidth, $sheight);
                $spimage->save("$file-$id.png");
                $cropped_items[] = "$file-$id.png";

                $data[$id] = ImageMapSerializer::serialize("$file-$id.png");

                $id++;
            }
        }

        foreach($cropped_items as $item){
            self::safeDeleteFile($item);
        }
        self::safeDeleteFile($newname);

        return $data;
    }

    public static function safeDeleteFile(string $item): bool{
        try{
            unlink($item);
        }catch(\Exception $ex){
            var_dump($ex->getMessage());
            return false;
        }finally{
            return true;
        }
    }

}
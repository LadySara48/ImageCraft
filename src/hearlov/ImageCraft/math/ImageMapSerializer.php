<?php

namespace hearlov\ImageCraft\math;

use pocketmine\color\Color;

class ImageMapSerializer{

    public static function serialize(string $file): array{
        $image = imagecreatefrompng($file);
        if($image === false) return [];
        if(get_class($image) !== "GdImage") return [];

        $args = [];
        for($x = 0; $x < 128; ++$x){
            for($y = 0; $y < 128; ++$y){
                $argb = imagecolorat($image, $x, $y);
                //$a = ($argb >> 24) & 0xff;
                $r = ($argb >> 16) & 0xff;
                $g = ($argb >> 8) & 0xff;
                $b = $argb & 0xff;
                $args[$y][$x] = new Color($r, $g, $b);
            }
        }
        imagedestroy($image);
        return $args;
    }

    public static function serializeArray(array $args): string{
        $data = "";
        for($x = 0; $x < 128; ++$x){
            for($y = 0; $y < 128; ++$y){
                /**
                 * @var Color $color
                 */
                $color = $args[$y][$x];
                $r = $color->getR();
                $g = $color->getG();
                $b = $color->getB();
                $data .= chr($r) . chr($g) . chr($b);
            }
        }

        return $data;
    }

    public static function unserializeString(string $str): array{
        $pos = 0;
        $arg = [];
        for($x = 0; $x < 128; ++$x){
            for($y = 0; $y < 128; ++$y){
                $r = ord($str[$pos]);
                $g = ord($str[$pos+1]);
                $b = ord($str[$pos+2]);
                $pos += 3;
                $arg[$y][$x] = new Color($r, $g, $b);
            }
        }
        return $arg;
    }

}
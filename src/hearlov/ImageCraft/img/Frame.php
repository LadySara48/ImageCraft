<?php

namespace hearlov\ImageCraft\img;

class Frame{

    protected array $datas = [];

    public function __construct(array $image_data){
        $this->datas = $image_data;
    }

    public function getData(): array{
        return $this->datas;
    }

}
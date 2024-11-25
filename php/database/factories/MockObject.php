<?php


namespace Database\Factories;




trait MockObject
{


    public function loadContent()
    {

        $toSearch = array_slice(explode("\\",$this->model),2);
        $path = base_path('database/mocks/'.implode(DIRECTORY_SEPARATOR,$toSearch));

        $files = glob($path.DIRECTORY_SEPARATOR."*.json");

        if(!is_array($files) || count($files) === 0){
            throw new \RuntimeException('Cant find files to mock');
        }
        $file = file_get_contents($files[array_rand($files)]);
        return json_decode($file,true);

    }

}

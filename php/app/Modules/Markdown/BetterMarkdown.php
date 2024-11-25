<?php


namespace App\Modules\Markdown;


use Illuminate\Support\Facades\Storage;
use Parsedown;

class BetterMarkdown
{

    public static function loadByFile(string $filename):BetterMarkdownResponse
    {
        $storage = Storage::disk('static');
        $data = $storage->get($filename);
        return (new static())->transform($data);
    }

    private function transform(string $content):BetterMarkdownResponse
    {
        $rsp = new BetterMarkdownResponse();
        $content = explode("\n",$content);
        if(trim($content[0])==="---"){
            $lines = count($content);
            $cur = 1;
            do{
                if(!isset($content[$cur]) || trim($content[$cur])==="---"){
                    break;
                }
                $line = explode(":",$content[$cur]);
                if(count($line)===2){
                    $rsp->addMeta(trim($line[0]),trim($line[1]));
                }
                $cur++;
            }while($cur<$lines);
            $content = implode("\n",array_slice($content,$cur));
        }
        $markdown = new Parsedown();
        $parsed = $markdown->parse($content);
        $rsp->setContent($parsed);
        return $rsp;
    }


}

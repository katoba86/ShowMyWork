<?php


namespace App\Modules\Scan;


use App\AppConfig;
use App\Models\Live\Page;
use App\Models\Live\PageKeywords;
use App\Models\Pool\PoolSite;
use App\Modules\Readin\WP\WpConverter;


class ScanCommand extends Command
{



    public $signature = 'app:scan {pid} {--N|noSave}';
    public $description = 'Scan for possible pages';

    public function handle(){


        $targets = [];
        $links = collect([]);
        $justScan = (bool)((int)$this->option('noSave')===1);
        $scanService = app()->make(ScanService::class);
        $scanService->setIsCli(true);
        $scanService->setNoSave($justScan);
        $scanService->setCliOutput($this->getOutput());

        if($justScan){



            $keyword = $this->ask('Search phrase?');
            $language = $this->choice("Which language",[
                'de'=>"German",
                'en'=>"English"
            ]);


            $k = new PageKeywords();
            $k->language = $language;
            $k->keyword = $keyword;
            $keywords = [$k];

        }else{

            $pid = (int)$this->argument('pid');
            $page = Page::with(['keywords'])->findOrFail($pid);


            $keywords = $page->keywords;
        }





        foreach($keywords as $keyword){
            $serpResults = $scanService->getLinksofGoogleSerp($keyword,2,AppConfig::SERP_SCAN_PAGES*10);
            $links = $links->merge($serpResults);
        }
        $links = $links->filter(fn($item)=>is_string($item))->unique();

        foreach($links as $link){
            $this->getOutput()->text("Try to validate\t".$link);
            if($scanService->validate($link)){
                $targets[] = $link;
            }
        }


        $this->output->success('Detected Pages');
        $this->output->table(['id','url'],
            array_map(
                function($item,$key){return [$key,$item];},$targets,array_keys($targets)));

        if(!$justScan) {

            if (count($targets) >= 2) {
                //$choice = $this->output->ask("Which page do you want to add?");
                $target = $this->output->choice("Which page do you want to add to this page?", $targets, null);

                if (null === $target) {
                    exit;
                }
            } elseif (count($targets) === 1) {
                $confirm = $this->ask("Continue with target \t" . $targets[0]);
                if (!$confirm) {
                    exit;
                }
                $target = $targets[0];
            } else {
                $this->warn("Nothing found. giving up. Add somet keywords!");
                exit;
            }


            $this->getOutput()->info('Starting for ' . $target);
            $page = $this->getOrCreatePage($target);
            $converter = new WpConverter($page);
            $converter->start();
            $this->info("Finished");


        }

    }

    private function getOrCreatePage(string $target):PoolSite
    {

        $page = PoolSite::firstOrCreate(["domain"=>$target],["domain"=>$target,'sourceLanguage'=>'en']);
        return $page;

    }

}

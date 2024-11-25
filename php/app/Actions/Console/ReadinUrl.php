<?php


namespace App\Actions\Console;



use App\Models\Pool\PoolSite;
use App\Modules\Readin\WP\WpConverter;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsCommand;

class ReadinUrl
{
    use AsCommand;
    public string $commandSignature = 'app:read {domain}';
    public string $commandDescription = 'Read domain into pool';
    public bool $commandHidden = false;

    public function handle(string $page)
    {
        $page = PoolSite::firstOrCreate(["domain"=>$page],["domain"=>$page,'sourceLanguage'=>'en']);
        $converter = new WpConverter($page);
        $converter->start();
    }

    public function asCommand(Command $command)
    {

        $page = trim($command->argument('domain'));
        $this->handle($page);


    }

}

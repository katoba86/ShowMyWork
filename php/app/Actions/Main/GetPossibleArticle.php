<?php


namespace App\Actions\Main;


use App\Models\Live\Page;
use App\Models\Pool\PoolArticle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class GetPossibleArticle
{
    use AsAction;


    /**
     * @param Page $page
     * @param int $num
     * @return iterable|Collection
     */
    public function handle(Page $page, int $num = 10): iterable|Collection
    {


        $keywords = collect(explode("\n", $page->catchphrase))->map(fn($item) => trim($item));
        $records = [];
        foreach ($keywords as $item) {
            if(!empty(trim($item))) {
                $records = [...DB::select(DB::raw($this->getSql()), ['k1' => $item, 'k2' => $item]), ...$records];
            }
        }

        if(count($records) === 0){
            $key = $page->keywords()->first();
            $records = [...DB::select(DB::raw($this->getSql()), ['k1' => $key->keyword, 'k2' => $key->keyword]), ...$records];
        }


        $records = collect($records)->sortByDesc(function ($i) {
            return (int)$i->relevance1 * 4 + (int)$i->relevance2 + (int)$i->isPreferred*100;
        })->unique('id')->take($num)->toArray();
        return PoolArticle::hydrate($records)->map(function(PoolArticle $article){
            $article->refresh();
            return $article;
        });
    }


    private function getSql(): string
    {
        return "SELECT pa.id,pa.slug,
                   MATCH (pa.`title`) AGAINST (:k1 IN NATURAL LANGUAGE MODE) AS relevance1,
                   MATCH (pa.`content`) AGAINST (:k2 IN NATURAL LANGUAGE MODE) AS relevance2,
                    isPreferred
                FROM pool_articles pa
                LEFT JOIN page_articles ON page_articles.article=pa.id
                WHERE
                    ISNULL(page_articles.article)
                HAVING relevance1 > 0 AND relevance2 > 0 LIMIT 0,100";
    }

}

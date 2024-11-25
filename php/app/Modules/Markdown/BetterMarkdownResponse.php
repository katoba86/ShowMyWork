<?php


namespace App\Modules\Markdown;


class BetterMarkdownResponse
{
    protected array $meta;

    protected string $content;


    public function __construct()
    {
        $this->meta = [];
        $this->content = "";
    }

    /**
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * @param array $meta
     * @return BetterMarkdownResponse
     */
    public function setMeta(array $meta): BetterMarkdownResponse
    {
        $this->meta = $meta;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return BetterMarkdownResponse
     */
    public function setContent(string $content): BetterMarkdownResponse
    {
        $this->content = $content;
        return $this;
    }

    public function addMeta(string $key, string $value)
    {
        $this->meta[$key] = $value;
    }

    /**
     * @param string $string
     * @return string
     * @throws \Exception
     */
    public function getMetaTag(string $string):string
    {
        if(!isset($this->meta[$string])){
            throw new \Exception($string." does not exist in meta_array");
        }
        return $this->meta[$string];
    }


}

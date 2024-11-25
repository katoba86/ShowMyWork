<?php
namespace App\Modules\Transform\Models;


use JetBrains\PhpStorm\Pure;

/**
 * Class TransformRequest
 * @package App\Modules\Transform\Models
 */
final class TransformRequest
{
    protected string $content;
    protected string $original;
    protected array $data = [];
    protected array $options = [];

    /**
     * @param string $content
     * @return TransformRequest
     */
    #[Pure] public static function fromStatic(string $content):TransformRequest
    {
        return new TransformRequest($content);
    }


    /**
     * @param string $key
     * @param mixed $value
     */
    public function addOption(string $key,mixed $value)
    {
        $this->options[$key] = $value;
    }

    #[Pure] public function getOption(string $key)
    {
        if(array_key_exists($key,$this->options)){
            return $this->options[$key];
        }
        return null;
    }

    /**
     * TransformRequest constructor.
     * @param string|null $content
     */
    public function __construct(string $content = null)
    {
        if(null !== $content){
            $this->content = $content;
        }
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
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getOriginal(): string
    {
        return $this->original;
    }

    /**
     * @param string $original
     */
    public function setOriginal(string $original): void
    {
        $this->original = $original;
    }

    /**
     * @param string $key
     * @return mixed
     */
    #[Pure] public function getData(string $key): mixed
    {
        if(array_key_exists($key,$this->data)){
            return $this->data[$key];
        }
        return null;
    }

    /**
     * @param string $identifer
     * @param mixed $data
     */
    public function setData(string $identifer,mixed $data): void
    {
        $this->data[$identifer] = $data;
    }

    public function getAllData():array
    {
        return $this->data;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options):void
    {
        $this->options = $options;
    }


}

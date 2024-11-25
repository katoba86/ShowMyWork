<?php


namespace App\Modules\Transform\Chains\DOM;


use App\Modules\Transform\Chains\AbstractChain;
use App\Modules\Transform\Chains\ChainInterface;
use App\Modules\Transform\Models\TransformRequest;

class ShortCodes extends AbstractChain implements ChainInterface
{

    private array $callbacks = [];

    public function handle(TransformRequest $transformRequest): TransformRequest
    {

        $this->callbacks = [
            'vc_row' => function ($attributes, $content, $tag) {
                return $content;
            },
            'vc_column' => function ($attributes, $content, $tag) {
                return $content;
            },
            'vc_raw_html' => function ($attributes, $content, $tag) {
                return '';
            },
            'vc_column_text' => function ($attributes, $content, $tag) {
                return $content;
            },
            'vc_raw_js' => function ($attributes, $content, $tag) {
                return '';
            }
        ];

        $transformRequest->setContent(
            $this->parse($transformRequest->getContent())
        );
        return $transformRequest;
    }


    public function parse($content)
    {

        $result = $content;

        $shortcodes = $this->getShortcodeList($content);
        foreach ($shortcodes as $shortcode) {
            // Only process known/supported shortcodes
            if (in_array($shortcode, array_keys($this->callbacks))) {

                $regexp = $this->getShortcodeRegexp($shortcode);

                $result = preg_replace_callback("/$regexp/s", array($this, 'parseSingle'), $result);
            }
        }

        return $result;
    }

    protected function getShortcodeList($content)
    {
        $result = array();

        preg_match_all("/\[([A-Za-z_]+[^\ \]]+)/", $content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $result[$match] = $match;
            }
        }
        return $result;
    }

    protected function getShortcodeRegexp($tagregexp)
    {

        return
            '\\['                              // Opening bracket
            . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
            . "($tagregexp)"                     // 2: Shortcode name
            . '(?![\\w-])'                       // Not followed by word character or hyphen
            . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
            . '[^\\]\\/]*'                   // Not a closing bracket or forward slash
            . '(?:'
            . '\\/(?!\\])'               // A forward slash not followed by a closing bracket
            . '[^\\]\\/]*'               // Not a closing bracket or forward slash
            . ')*?'
            . ')'
            . '(?:'
            . '(\\/)'                        // 4: Self closing tag ...
            . '\\]'                          // ... and closing bracket
            . '|'
            . '\\]'                          // Closing bracket
            . '(?:'
            . '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
            . '[^\\[]*+'             // Not an opening bracket
            . '(?:'
            . '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
            . '[^\\[]*+'         // Not an opening bracket
            . ')*+'
            . ')'
            . '\\[\\/\\2\\]'             // Closing shortcode tag
            . ')?'
            . ')'
            . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
    }

    public function runStandalone(string $content)
    {
        $transformRequest = new TransformRequest();
        $transformRequest->setContent($content);
        $transformRequest = $this->handle($transformRequest);
        return $transformRequest->getContent();

    }

    protected function parseSingle($m)
    {

        // allow [[foo]] syntax for escaping a tag
        if ($m[1] == '[' && $m[6] == ']') {
            return substr($m[0], 1, -1);
        }

        $tag = $m[2];
        $attr = $this->shortcodeParseAtts($m[3]);

        if ($attr === '') {
            $attr = null;
        }

        if (isset($m[5])) {
            // enclosing tag - extra parameter

            return $m[1] . call_user_func($this->callbacks[$tag], $attr, $m[5], $tag) . $m[6];
        } else {
            // self-closing tag
            return $m[1] . call_user_func($this->callbacks[$tag], $attr, null, $tag) . $m[6];
        }
    }

    protected function shortcodeParseAtts($text)
    {
        $atts = array();
        $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
        $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
        if (preg_match_all($pattern, $text, $match, PREG_SET_ORDER)) {
            foreach ($match as $m) {
                if (!empty($m[1]))
                    $atts[strtolower($m[1])] = stripcslashes($m[2]);
                elseif (!empty($m[3]))
                    $atts[strtolower($m[3])] = stripcslashes($m[4]);
                elseif (!empty($m[5]))
                    $atts[strtolower($m[5])] = stripcslashes($m[6]);
                elseif (isset($m[7]) and strlen($m[7]))
                    $atts[] = stripcslashes($m[7]);
                elseif (isset($m[8]))
                    $atts[] = stripcslashes($m[8]);
            }
        } else {
            $atts = ltrim($text);
        }
        return $atts;
    }

}

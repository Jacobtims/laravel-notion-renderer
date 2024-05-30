<?php

namespace RehanKanak\LaravelNotionRenderer\Blocks;

class Code extends Block
{
    public string $language;

    public function __construct($block, $previousBlock)
    {
        parent::__construct($block, $previousBlock);

        $this->language = $block['code']['language'];
    }

    private function start(): void
    {
        $this->result .= '<pre><code class="language-'.$this->language.'">';
    }

    private function end(): void
    {
        $this->result .= '</code></pre>';
    }

    public function process(): Code
    {
        Code::start();

        foreach ($this->block['code']['rich_text'] as $content) {
            $this->result .= $content['text']['content'];
        }

        Code::end();

        return $this;
    }

    public function render(): string
    {
        $this->previousBlock = $this->block;

        return $this->result;
    }
}

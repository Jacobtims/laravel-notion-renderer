<?php

namespace RehanKanak\LaravelNotionRenderer\Blocks;

class Paragraph extends Block
{
    private function start(): void
    {
        $this->result .= '<p>';
    }

    private function end(): void
    {
        $this->result .= '</p>';
    }

    public function process(): Paragraph
    {
        Paragraph::start();

        foreach ($this->block['paragraph']['rich_text'] as $content) {
            $prefix = '';
            $suffix = '';

            // Apply the different annotations
            if ($content['annotations']['bold'] === true) {
                $prefix = $prefix.'<strong>';
                $suffix = '</strong>'.$suffix;
            }
            if ($content['annotations']['italic'] === true) {
                $prefix = $prefix.'<i>';
                $suffix = '</i>'.$suffix;
            }
            if ($content['annotations']['strikethrough'] === true) {
                $prefix = $prefix.'<span style="text-decoration: line-through;">';
                $suffix = '</span>'.$suffix;
            }
            if ($content['annotations']['underline'] === true) {
                $prefix = $prefix.'<u>';
                $suffix = '</u>'.$suffix;
            }
            if ($content['annotations']['code'] === true) {
                $prefix = $prefix.'<code>';
                $suffix = '</code>'.$suffix;
            }

            if ($content['text']['link'] !== null) {
                $text = "<a target='_blank' href=".$content['text']['link']['url'].'>'.$content['text']['content'].'</a>';
            } else {
                $text = $content['text']['content'];
            }

            $this->result .= $prefix.$text.$suffix;
        }

        Paragraph::end();

        return $this;
    }

    public function render(): string
    {
        $this->previousBlock = $this->block;

        return $this->result;
    }
}

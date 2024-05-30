<?php

namespace RehanKanak\LaravelNotionRenderer\Blocks;

use RehanKanak\LaravelNotionRenderer\Exceptions\NotionException;
use RehanKanak\LaravelNotionRenderer\Http\Integrations\Notion\NotionConnector;
use RehanKanak\LaravelNotionRenderer\Http\Integrations\Notion\Requests\GetBlockChildrenRequest;

class Table extends Block
{
    public $children = null;

    private function start(): void
    {
        $this->result .= '<table>';
    }

    private function end(): void
    {
        $this->result .= '</table>';
    }

    /**
     * @throws NotionException
     */
    public function children(): Table
    {
        $connector = new NotionConnector();
        $request = new GetBlockChildrenRequest($this->block['id']);

        $paginator = $connector->paginate($request);

        $this->children = $paginator->items();

        return $this;
    }

    public function process(): Table
    {
        if (! $this->children) {
            $this->result = '';

            return $this;
        }

        Table::start();

        foreach ($this->children as $rowIndex => $row) {
            if ($row['type'] === 'table_row') {
                $this->result .= '<tr>';
                foreach ($row['table_row']['cells'] as $cellIndex => $cell) {
                    if (($rowIndex === 0 && $this->block['table']['has_column_header']) || ($cellIndex === 0 && $this->block['table']['has_row_header'])) {
                        $this->result .= '<th>';
                    } else {
                        $this->result .= '<td>';
                    }

                    foreach ($cell as $content) {
                        if ($content['text']['link'] !== null) {
                            $this->result .= "<a target='_blank' href=".$content['text']['link']['url'].'>'.$content['text']['content'].'</a>';
                        } else {
                            $this->result .= $content['text']['content'];
                        }
                    }

                    if (($rowIndex === 0 && $this->block['table']['has_column_header']) || ($cellIndex === 0 && $this->block['table']['has_row_header'])) {
                        $this->result .= '</th>';
                    } else {
                        $this->result .= '</td>';
                    }
                }
                $this->result .= '</tr>';
            }
        }

        Table::end();

        return $this;
    }

    public function render(): string
    {
        $this->previousBlock = $this->block;

        return $this->result;
    }
}

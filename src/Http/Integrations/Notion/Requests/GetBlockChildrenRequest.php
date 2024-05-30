<?php

namespace RehanKanak\LaravelNotionRenderer\Http\Integrations\Notion\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class GetBlockChildrenRequest extends Request implements Paginatable
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $pageId,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return "/blocks/$this->pageId/children";
    }
}

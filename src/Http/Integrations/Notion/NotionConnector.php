<?php

namespace RehanKanak\LaravelNotionRenderer\Http\Integrations\Notion;

use Saloon\Enums\Method;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\HasPagination;
use Saloon\PaginationPlugin\CursorPaginator;
use Saloon\PaginationPlugin\Paginator;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

class NotionConnector extends Connector implements HasPagination
{
    use AlwaysThrowOnErrors;

    public function resolveBaseUrl(): string
    {
        return 'https://api.notion.com/v1';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Notion-Version' => config('notion-renderer.NOTION_API_VERSION'),
        ];
    }

    protected function defaultAuth(): TokenAuthenticator
    {
        $token = config('notion-renderer.NOTION_API');

        return new TokenAuthenticator($token);
    }

    public function paginate(Request $request): Paginator
    {
        return new class(connector: $this, request: $request) extends CursorPaginator
        {
            protected function getNextCursor(Response $response): int|string
            {
                return $response->json('next_cursor');
            }

            protected function isLastPage(Response $response): bool
            {
                return is_null($response->json('next_cursor'));
            }

            protected function getPageItems(Response $response, Request $request): array
            {
                return $response->json('results');
            }

            protected function applyPagination(Request $request): Request
            {
                if ($this->currentResponse instanceof Response) {
                    if ($request->getMethod() === Method::POST) {
                        $request->body()->add('start_cursor', $this->getNextCursor($this->currentResponse));
                    }
                    if ($request->getMethod() === Method::GET) {
                        $request->query()->add('start_cursor', $this->getNextCursor($this->currentResponse));
                    }
                }

                if (isset($this->perPageLimit)) {
                    if ($request->getMethod() === Method::POST) {
                        $request->body()->add('page_size', $this->perPageLimit);
                    }
                    if ($request->getMethod() === Method::GET) {
                        $request->query()->add('page_size', $this->perPageLimit);
                    }
                }

                return $request;
            }
        };
    }
}

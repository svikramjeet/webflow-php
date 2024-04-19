<?php

namespace Svikramjeet\Webflow;

class Webflow
{
    private const API_ENDPOINT = 'https://api.webflow.com/v2';

    private string $token;

    private array $cache = [];

    public function __construct()
    {
        $token = config('webflow.token');
        if (empty($token)) {
            throw new WebflowException('Token cannot be empty.');
        }
        $this->token = $token;
    }

    private function request(string $path, string $method, array $data = []): mixed
    {
        $curl = curl_init();
        $options = [
            CURLOPT_URL => self::API_ENDPOINT.$path,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$this->token}",
                'Accept: application/json',
                'Content-Type: application/json',
            ],
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
        ];
        if (! empty($data)) {
            $json = json_encode($data);
            $options[CURLOPT_POSTFIELDS] = $json;
            $options[CURLOPT_HTTPHEADER][] = 'Content-Length: '.strlen($json);
        }
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        curl_close($curl);
        [, $body] = explode("\r\n\r\n", $response, 2);

        return json_decode($body);
    }

    private function get(string $path): mixed
    {
        return $this->request($path, 'GET');
    }

    private function post(string $path, array $data): mixed
    {
        return $this->request($path, 'POST', $data);
    }

    private function put(string $path, array $data): mixed
    {
        return $this->request($path, 'PUT', $data);
    }

    private function delete(string $path): mixed
    {
        return $this->request($path, 'DELETE');
    }

    public function info(): mixed
    {
        return $this->get('/info');
    }

    public function sites(): mixed
    {
        return $this->get('/sites');
    }

    public function site(string $siteId): mixed
    {
        return $this->get("/sites/{$siteId}");
    }

    public function domains(string $siteId): mixed
    {
        return $this->get("/sites/{$siteId}/domains");
    }

    public function publishSite(string $siteId, array $domains): mixed
    {
        return $this->post("/sites/${siteId}/publish", $domains);
    }

    public function collections(string $siteId): mixed
    {
        return $this->get("/sites/{$siteId}/collections");
    }

    public function collection(string $collectionId): mixed
    {
        return $this->get("/collections/{$collectionId}");
    }

    public function items(string $collectionId, int $offset = 0, int $limit = 100): mixed
    {
        $query = http_build_query([
            'offset' => $offset,
            'limit' => $limit,
        ]);

        return $this->get("/collections/{$collectionId}/items?{$query}");
    }

    public function itemsAll(string $collectionId): array
    {
        $response = $this->items($collectionId);
        $items = $response->items;
        $limit = $response->limit;
        $total = $response->total;
        $pages = ceil($total / $limit);
        for ($page = 1; $page < $pages; $page++) {
            $offset = $response->limit * $page;
            $items = array_merge($items, $this->items($collectionId, $offset, $limit)->items);
        }

        return $items;
    }

    public function item(string $collectionId, string $itemId): mixed
    {
        return $this->get("/collections/{$collectionId}/items/{$itemId}");
    }

    public function createItem(string $collectionId, array $fields, bool $live = false): mixed
    {
        $defaults = [
            '_archived' => false,
            '_draft' => false,
        ];

        return $this->post("/collections/{$collectionId}/items".($live ? '?live=true' : ''), [
            'fields' => array_merge($defaults, $fields),
        ]);
    }

    public function updateItem(string $collectionId, string $itemId, array $fields, bool $live = false): mixed
    {
        return $this->put("/collections/{$collectionId}/items/{$itemId}".($live ? '?live=true' : ''), [
            'fields' => $fields,
        ]);
    }

    public function removeItem(string $collectionId, $itemId): mixed
    {
        return $this->delete("/collections/{$collectionId}/items/{$itemId}");
    }

    public function findOrCreateItemByName(string $collectionId, array $fields): mixed
    {
        if (! isset($fields['name'])) {
            throw new WebflowException('Name field is required.');
        }
        $cacheKey = "collection-{$collectionId}-items";
        $instance = $this;
        $items = $this->cache($cacheKey, function () use ($instance, $collectionId) {
            return $instance->itemsAll($collectionId);
        });
        foreach ($items as $item) {
            if (strcasecmp($item->name, $fields['name']) === 0) {
                return $item;
            }
        }
        $newItem = $this->createItem($collectionId, $fields);
        $items[] = $newItem;
        $this->cacheSet($cacheKey, $items);

        return $newItem;
    }

    private function cache(string $key, callable $callback): mixed
    {
        if (! isset($this->cache[$key])) {
            $this->cache[$key] = $callback();
        }

        return $this->cache[$key];
    }

    private function cacheSet(string $key, $value): void
    {
        $this->cache[$key] = $value;
    }
}

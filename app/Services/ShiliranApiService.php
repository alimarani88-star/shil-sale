<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ShiliranApiService implements ShiliranApiInterface
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.shiliran.base_uri', 'https://app.shiliran.ir');
        $this->apiKey  = config('services.shiliran.key', env('GLOBAL_API_KEY'));
    }

    protected function client()
    {
        return Http::withHeaders([
            'X-API-KEY' => $this->apiKey,
        ])
            ->withOptions([
                'verify' => config('services.shiliran.cert'),
            ])
            
            ;

    }

    protected function request(string $endpoint): array
    {
        try {
            $response = $this->client()->get("{$this->baseUrl}/{$endpoint}");

            $json = $response->json();


            if (!is_array($json)) {
                return [
                    'status'  => 'error',
                    'message' => 'دریافت اطلاعات ناموفق بود (فرمت غیر معتبر)',
                    'data'    => [],
                ];
            }

            return [
                'status'  => ($json['status'] ?? 'error') === 'success',
                'message' => $json['message'] ?? '',
                'data'    => $json['data'] ?? [],
            ];

        } catch (\Exception $e) {

            return [
                'status'  => 'error',
                'message' => 'اشکال در برقراری ارتباط: ' . $e->getMessage(),
                'data'    => [],
            ];
        }
    }

    public function getMainGroups(): array
    {
        return $this->request('api_get_main_groups');
    }

    public function getGroups(): array
    {
        return $this->request('api_get_groups');
    }

    public function getGroupById(int $id): array
    {
        return $this->request("api_get_group_by_id/{$id}");
    }

    public function getItems(): array
    {
        return $this->request('api_get_items');
    }

    public function getItemById(int $id): array
    {
        return $this->request("api_get_item_by_id/{$id}");
    }

    public function getGroupsByMainGroupId(int $id): array
    {
        return $this->request("api_get_group_by_main_group_id/{$id}");
    }

    public function getItemsByGroupId(int $id): array
    {
        return $this->request("api_get_item_by_group_id/{$id}");
    }

    public function getMainGroupById(int $id): array
    {
        return $this->request("api_get_main_group_by_id/{$id}");
    }
}

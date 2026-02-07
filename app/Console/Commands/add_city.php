<?php

namespace App\Console\Commands;

use App\Models\ProvinceCity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Log;

class add_city extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add_city';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = "https://api.postex.ir/api/v1/locality/cities/all";

        try {
            $response = Http::get($url);

            if (!$response->successful()) {
                $this->error("❌ خطا در ارتباط با API. کد وضعیت: " . $response->status());
                return;
            }

            $data = $response->json();
            $totalCities = 0;

            foreach ($data as $province) {

                // استان را ذخیره کن یا بیاور
                $provinceModel = ProvinceCity::create(
                    [
                        'postex_id' => $province['id'],
                        'title' => $province['name'],
                        'parent' => null,
                    ]
                );

                foreach ($province['cities'] as $city) {

                    ProvinceCity::create(
                        [
                            'postex_id' => $city['id'],
                            'title' => $city['name'],
                            'parent' => $provinceModel->id, // 👈 ID دیتابیس نه ID API
                        ]
                    );
                }
            }



            $this->info("🎉 عملیات کامل شد. مجموع شهرهای جدید: {$totalCities}");

        } catch (\Exception $e) {
            $this->error("⚠️ خطا در اجرای دستور: " . $e->getMessage());
            Log::error($e);
        }
    }
}

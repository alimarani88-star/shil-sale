<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BackfillProductSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:backfill-slugs
                            {--chunk=200 : Number of records to process per chunk}
                            {--dry-run : Show changes without saving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill missing product slugs from product_name';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $chunkSize = max(1, (int) $this->option('chunk'));
        $dryRun = (bool) $this->option('dry-run');

        $reservedSlugs = Product::query()
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->pluck('slug')
            ->map(fn ($slug) => Str::lower((string) $slug))
            ->flip()
            ->all();

        $query = Product::withTrashed()
            ->where(function ($q) {
                $q->whereNull('slug')->orWhere('slug', '');
            })
            ->select(['id', 'product_name', 'slug']);

        $totalCandidates = (clone $query)->count();

        if ($totalCandidates === 0) {
            $this->info('No products with empty slug were found.');
            return self::SUCCESS;
        }

        $this->info("Found {$totalCandidates} products with empty slug.");
        if ($dryRun) {
            $this->warn('Dry-run mode is ON. No data will be saved.');
        }

        $processed = 0;
        $updated = 0;

        $query->chunkById($chunkSize, function ($products) use (&$processed, &$updated, &$reservedSlugs, $dryRun) {
            foreach ($products as $product) {
                $processed++;

                $slugSource = $this->buildSlugSourceFromKeywords((string) $product->product_name);
                $baseSlug = Str::slug($slugSource);
                if ($baseSlug === '') {
                    $baseSlug = "product-{$product->id}";
                }

                $uniqueSlug = $this->makeUniqueSlug($baseSlug, $reservedSlugs);

                if (! $dryRun) {
                    $product->slug = $uniqueSlug;
                    $product->saveQuietly();
                }

                $updated++;
            }
        });

        $this->line("Processed: {$processed}");
        $this->info("Slugs generated: {$updated}");

        return self::SUCCESS;
    }


    private function buildSlugSourceFromKeywords(string $name): string
    {
        $normalizedName = str_replace(
            ['ك', 'ي', '‌'],
            ['ک', 'ی', ' '],
            $name
        );

        foreach ($this->getSlugReplacements() as $keyword => $replacement) {
            $normalizedName = str_replace($keyword, $replacement, $normalizedName);
        }

        $normalizedName = preg_replace('/\s+/u', ' ', $normalizedName) ?? $normalizedName;

        return trim($normalizedName);
    }

    private function getSlugReplacements(): array
    {
        static $cachedReplacements = null;
        if ($cachedReplacements !== null) {
            return $cachedReplacements;
        }
        

      
        $slugReplacements = [
            // برند
            'شیل ایران' => 'shiliran',
            'شیل' => 'shil',
            'ایران' => 'iran',

            // کلیدها
            'کلید مینیاتوری تک فاز' => 'single-phase-miniature-circuit-breaker',
            'کلید مینیاتوری تک پل' => 'single-pole-miniature-circuit-breaker',
            'کلید مینیاتوری سه پل' => 'three-pole-miniature-circuit-breaker',
            'کلید مینیاتوری' => 'miniature-circuit-breaker',

            'کلید اتوماتیک فیکس موتور دار' => 'fixed-motorized-mccb',
            'کلید اتوماتیک فیکس' => 'fixed-mccb',
            'کلید اتوماتیک' => 'mccb',

            'کلید' => 'switch',
            'مینیاتوری' => 'miniature',
            'اتوماتیک' => 'automatic',
            'فیکس' => 'fixed',
            'موتور دار' => 'motorized',
            'موتوری' => 'motorized',

            // فاز و پل
            'تک فاز' => 'single-phase',
            'سه فاز' => 'three-phase',
            'تک پل' => 'single-pole',
            'سه پل' => 'three-pole',
            'پل' => 'pole',
            'فاز' => 'phase',

            // مشخصات الکتریکی
            'آمپر' => 'amp',
            'کیلو وات' => 'kw',
            'ولت' => 'voltage',
            'وات' => 'watt',
            'قدرت قطع' => 'breaking-capacity',
            'قطع' => 'breaking',
            'تیپ' => 'type',

            // تجهیزات برق
            'چراغ سیگنال' => 'signal-lamp',
            'کنتاکتور' => 'contactor',
            'استابلایزر تک فاز' => 'single-phase-stabilizer',
            'استابلایزر سه فاز' => 'three-phase-stabilizer',
            'استابلایزر' => 'stabilizer',
            'اینورتر سه فاز' => 'three-phase-inverter',
            'اینورتر خورشیدی' => 'solar-inverter',
            'اینورتر' => 'inverter',

            // سروو و دیجیتال
            'دیجیتال سروو موتوری' => 'digital-servo-motorized',
            'سروو موتوری ایستاده' => 'standing-servo-motorized',
            'دیجیتال' => 'digital',
            'سروو' => 'servo',
            'ایستاده' => 'standing',

            // شیرینگ و کابل‌بند
            'شیرینگ حرارتی ارت زرد و سبز' => 'earth-yellow-green-heat-shrink',
            'شیرینگ حرارتی مشکی' => 'black-heat-shrink',
            'شیرینگ حرارتی زرد' => 'yellow-heat-shrink',
            'شیرینگ حرارتی قرمز' => 'red-heat-shrink',
            'شیرینگ حرارتی آبی' => 'blue-heat-shrink',
            'شیرینگ حرارتی سبز' => 'green-heat-shrink',
            'شیرینگ حرارتی' => 'heat-shrink',
            'شیرینگ' => 'shrink',
            'حرارتی' => 'heat',
            'بست کمربندی' => 'cable-tie',
            'بست' => 'tie',
            'کمربندی' => 'cable-tie',

            // شستی و فرمان
            'شستی جرثقیل ضد آب تک سرعته' => 'waterproof-single-speed-crane-push-button',
            'شستی جرثقیل ضد آب' => 'waterproof-crane-push-button',
            'شستی جرثقیل' => 'crane-push-button',
            'ضد آب' => 'waterproof',
            'تک سرعته' => 'single-speed',
            'شستی' => 'push-button',
            'جرثقیل' => 'crane',
            'فرمان' => 'control',

            // کنتاکت
            'کنتاکت باز' => 'normally-open-contact',
            'کنتاکت بسته' => 'normally-closed-contact',
            'کنتاکت' => 'contact',
            'باز' => 'open',
            'بسته' => 'closed',

            // رنگ‌ها
            'ارت زرد و سبز' => 'earth-yellow-green',
            'زرد و سبز' => 'yellow-green',
            'مشکی' => 'black',
            'سفید' => 'white',
            'قرمز' => 'red',
            'زرد' => 'yellow',
            'آبی' => 'blue',
            'سبز' => 'green',

            // واحدها و اندازه‌ها
            'میلی متر' => 'mm',
            'متری' => 'meter',
            'متر' => 'meter',
            'سایز' => 'size',
            'عرض' => 'width',

            // سایر کلمات پرتکرار
            'باکالیتی' => 'bakelite',
            'دار' => 'with',
            'مدل' => 'model',
        ];


        uksort($slugReplacements, function (string $left, string $right): int {
            $leftLength = function_exists('mb_strlen') ? mb_strlen($left, 'UTF-8') : strlen($left);
            $rightLength = function_exists('mb_strlen') ? mb_strlen($right, 'UTF-8') : strlen($right);
            return $rightLength <=> $leftLength;
        });
        $cachedReplacements = $slugReplacements;
        return $cachedReplacements;
    }
    private function makeUniqueSlug(string $baseSlug, array &$reservedSlugs): string
    {
        $candidate = $baseSlug;
        $suffix = 2;

        while (isset($reservedSlugs[Str::lower($candidate)])) {
            $candidate = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        $reservedSlugs[Str::lower($candidate)] = true;

        return $candidate;
    }
}

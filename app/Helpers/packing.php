<?php

use App\Models\Packing_pattern;
use App\Models\Product;
use App\Models\Product_carton;
use App\Models\Standard_carton;

/**
 * محاسبه بسته بندی محصولات داخل کارتن‌ها با چیدمان سه‌بعدی
 */
function getTotalPack($carts)
{
    $standard_cartons = Standard_carton::all()->keyBy('id')->toArray();
    $company_packaged = [];
    foreach ($carts as &$cart) {
        $product_id_in_app = $cart->product->product_id_in_app ?? null;
        $group_id_in_app = $cart->product->group_id_in_app ?? null;

        if ($product_id_in_app && $group_id_in_app) {
//            جست کارتن مناسب برای محصول
            $company_cartons = Product_carton::where('item_id', $product_id_in_app)
                ->get();
            if (count($company_cartons) == 0) {
                $company_cartons = Product_carton::where('group_id', $group_id_in_app)
                    ->get();
            }
            if (count($company_cartons) > 0) {
                $cartonSizes = $company_cartons->pluck('max_number')->toArray();
                $count = $cart->count;
                $selectedSizes = findOptimalCartons($count, $cartonSizes);
                $_cartons = [];
                foreach ($company_cartons as $_carton) {
                    if (isset($selectedSizes[$_carton->max_number])) {
                        $_cartons[] =
                            [
                                'carton_id' => $_carton->carton_id,
                                'carton_count' => $selectedSizes[$_carton->max_number],
                                'carton_name' => $standard_cartons[$_carton->carton_id]['name'],
                                'carton_weight' => $standard_cartons[$_carton->carton_id]['box_weight'],
                                'carton_width' => $standard_cartons[$_carton->carton_id]['width'],
                                'carton_height' => $standard_cartons[$_carton->carton_id]['height'],
                                'carton_length' => $standard_cartons[$_carton->carton_id]['length'],

                            ];
                    }
                }
// محاسبه وزن کل کارتن‌ها
                $total_cartons_weight = 0;
                foreach ($_cartons as $carton) {
                    $total_cartons_weight += intval($carton['carton_count']) * intval($carton['carton_weight']);
                }
                $company_packaged[$cart->id] = [
                    'cart_id' => $cart->id,
                    'product_id_in_app' => $cart->product->product_id_in_app,
                    'product_id' => $cart->product->id,
                    'product_name' => $cart->product->product_name,
                    'product_count' => $cart->count,
                    'product_weight' => $cart->product->weight,
                    'product_total_weight' => intval($cart->count) * intval($cart->product->weight),
                    'total_packaged_weight' => intval($cart->count) * intval($cart->product->weight) + $total_cartons_weight,
                    'cartons' => $_cartons,
                ];
            } elseif (count($company_cartons) == 0) {
                $defaultCarton = Standard_carton::where('type' , 'default')->first();
                $_carton =
                    [
                        'carton_id' => $defaultCarton->carton_id,
                        'carton_count' => 1,
                        'carton_name' => $defaultCarton->name,
                        'carton_weight' => $defaultCarton->box_weight,
                        'carton_width' => $defaultCarton->width,
                        'carton_height' =>$defaultCarton->height,
                        'carton_length' =>$defaultCarton->length,

                    ];
                $company_packaged[$cart->id] = [
                    'cart_id' => $cart->id,
                    'product_id_in_app' => $cart->product->product_id_in_app,
                    'product_id' => $cart->product->id,
                    'product_name' => $cart->product->product_name,
                    'product_count' => $cart->count,
                    'product_weight' => $cart->product->weight,
                    'product_total_weight' => intval($cart->count) * intval($cart->product->weight),
                    'total_packaged_weight' => intval($cart->count) * intval($cart->product->weight),
                    'cartons' => [$_carton],
                    'error' => 'هیچ کارتنی برای این محصول تعریف نشده است',
                ];
            }
        } else {
            $company_packaged[$cart->id] = [
                'error' => $cart->product->id . '-' . $cart->id . 'product_id_in_app و group_id_in_app تعریف نشده است ',
            ];

        }
    }
    return getTotalPostPack($company_packaged);

}

function findOptimalCartons($count, $cartonCapacities)
{
    if ($count <= 0) return [];
    $capacities = array_unique(array_filter($cartonCapacities, fn($s) => $s > 0));
    rsort($capacities); // بزرگ به کوچک
    if (empty($capacities)) return [];
    $cartons = [];
    $new_count = $count;
//    اولویت اول : چک کردن بخش پذیر بودن تمام کارتن ها بر تعداد درخواستی
//    foreach ($capacities as $index => $capacity) {
//        if($new_count%$capacity==0) {
//            $cartons[$capacity] = $new_count/$capacity;
//        }
//    }
    if (empty($cartons)) {
        //    اولویت دوم : تقسیم های متوالی
        foreach ($capacities as $index => $capacity) {
            if ($new_count >= $capacity && $new_count > 0) {
                $cartons[$capacity] = floor($new_count / $capacity);
                $new_count = $new_count % $capacity;
            }
            if ($index + 1 == count($capacities) && $new_count < $capacity && $new_count > 0) {
                if (isset($cartons[$capacity])) {
                    $cartons[$capacity]++;
                } else {
                    $cartons[$capacity] = 1;
                }
            }
        }
    }
    return $cartons;
}

function getTotalPostPack($packages = [])
{
    if (empty($packages)) {
        return [
            "width" => 100,
            "height" => 100,
            "length" => 100,
            "weight" => 10000,
            "name" => 'default',
            "id_post" => null,
        ];
    };
    $cartons = [];
    $packages_weight = 0;
    foreach ($packages as $package) {
        $packages_weight += $package['total_packaged_weight'];
        foreach ($package['cartons'] as $carton) {
            if (isset($cartons[$carton['carton_id']])) {
                $cartons[$carton['carton_id']] = intval($carton['carton_count']) + $cartons[$carton['carton_id']];
            } else {
                $cartons[$carton['carton_id']] = intval($carton['carton_count']);
            }

        }
    }
    $matchedPatterns = [];
    $patterns = Packing_pattern::with('details')->get();
    foreach ($patterns as $pattern) {
        $ok = true;
        foreach ($cartons as $cartonId => $quantity) {
            $detail = $pattern->details->firstWhere('carton_id', $cartonId);
            if (!$detail || $detail->quantity < $quantity) {
                $ok = false;
                break;
            }
        }
        if ($ok) {
            $matchedPatterns[] = $pattern;
        }
    }
    $selectedCarton = null;
    if (empty($matchedPatterns)) {
        $calculateMinimumDimensions = calculateMinimumDimensions($packages);
        $selectedCarton =$calculateMinimumDimensions['best_method'] ;
        $selectedCarton['weight'] =  intval($packages_weight) ;
        $selectedCarton['name'] =  '+9' ;
        $selectedCarton['id_post'] =  '13' ;
    } else {
        $minVolume = null;
        foreach ($matchedPatterns as $pattern) {
            $standard_carton = Standard_carton::find($pattern->carton_id);
            $volume = intval($standard_carton->length) * intval($standard_carton->width) * intval($standard_carton->height);
            if ($minVolume === null || $volume < $minVolume) {
                $minVolume = $volume;
                $selectedCarton = [
                    "width" => $standard_carton->width,
                    "height" => $standard_carton->height,
                    "length" => $standard_carton->length,
                    "id_post" => $standard_carton->id_post,
                    "weight" => intval($standard_carton->box_weight) + intval($packages_weight),
                    "name" => $standard_carton->name,
                ];
            }
        }
    }
    if ($selectedCarton) {
        return $selectedCarton;
    } else {
        return [
            "width" => 100,
            "height" => 100,
            "length" => 100,
            "weight" => intval($packages_weight),
            "name" => 'default',
            "id_post" => null,
        ];
    }
}

function calculateMinimumDimensions($packages)
{
    $allCartons = [];
    // جمع‌آوری تمام کارتن‌ها
    foreach ($packages as $package) {
        if (!isset($package['cartons']) || !is_array($package['cartons'])) {
            continue;
        }
        foreach ($package['cartons'] as $carton) {
            $count = (int)$carton['carton_count'];
            for ($i = 0; $i < $count; $i++) {
                $allCartons[] = [
                    'length' => $carton['carton_length'],
                    'width' => $carton['carton_width'],
                    'height' => $carton['carton_height'],
                    'name' => $carton['carton_name'],
                    'volume' => intval($carton['carton_length']) * intval($carton['carton_width']) * intval($carton['carton_height'])
                ];
            }
        }
    }

    if (empty($allCartons)) {
        return [
            'error' => true,
            'message' => 'هیچ کارتنی یافت نشد یا ابعاد کارتن‌ها قابل استخراج نیست'
        ];
    }

    $cartonCount = count($allCartons);

    // روش ۱: چیدمان عمودی (روی هم) - کوچکترین مساحت کف
    $maxLength1 = max(array_column($allCartons, 'length'));
    $maxWidth1 = max(array_column($allCartons, 'width'));
    $totalHeight1 = array_sum(array_column($allCartons, 'height'));

    $method1 = [
        'length' => $maxLength1,
        'width' => $maxWidth1,
        'height' => $totalHeight1,
        'volume' => $maxLength1 * $maxWidth1 * $totalHeight1,
        'method' => 'عمودی - روی هم (Stack Vertical)'
    ];

    // روش ۲: چیدمان افقی در یک ردیف
    $totalLength2 = array_sum(array_column($allCartons, 'length'));
    $maxWidth2 = max(array_column($allCartons, 'width'));
    $maxHeight2 = max(array_column($allCartons, 'height'));

    $method2 = [
        'length' => $totalLength2,
        'width' => $maxWidth2,
        'height' => $maxHeight2,
        'volume' => $totalLength2 * $maxWidth2 * $maxHeight2,
        'method' => 'افقی - یک ردیف (Single Row)'
    ];

    // روش ۳: چیدمان ماتریسی 2x2 یا بهینه
    $columns = ceil(sqrt($cartonCount));
    $rows = ceil($cartonCount / $columns);

    $avgLength = array_sum(array_column($allCartons, 'length')) / $cartonCount;
    $avgWidth = array_sum(array_column($allCartons, 'width')) / $cartonCount;
    $maxHeight3 = max(array_column($allCartons, 'height'));

    $method3 = [
        'length' => $avgLength * $columns,
        'width' => $avgWidth * $rows,
        'height' => $maxHeight3,
        'volume' => $avgLength * $columns * $avgWidth * $rows * $maxHeight3,
        'method' => "ماتریسی {$rows}×{$columns} (Matrix Layout)"
    ];

    // روش ۴: چیدمان دو ردیفه
    if ($cartonCount >= 2) {
        $halfCount = ceil($cartonCount / 2);
        $avgLength4 = array_sum(array_column($allCartons, 'length')) / $cartonCount;
        $maxWidth4 = max(array_column($allCartons, 'width'));
        $totalHeight4 = max(array_column($allCartons, 'height')) * 2;

        $method4 = [
            'length' => $avgLength4 * $halfCount,
            'width' => $maxWidth4,
            'height' => $totalHeight4,
            'volume' => $avgLength4 * $halfCount * $maxWidth4 * $totalHeight4,
            'method' => 'دو ردیفه (Two Rows)'
        ];
    } else {
        $method4 = $method1;
    }

    // مقایسه و انتخاب بهترین روش
    $methods = [$method1, $method2, $method3, $method4];
    usort($methods, function ($a, $b) {
        return $a['volume'] <=> $b['volume'];
    });

    // محاسبه حجم کل واقعی کارتن‌ها
    $totalCartonsVolume = array_sum(array_column($allCartons, 'volume'));

    return [
        'error' => false,
        'best_method' => $methods[0],
        'all_methods' => $methods,
        'statistics' => [
            'total_cartons' => $cartonCount,
            'total_cartons_volume' => $totalCartonsVolume,
            'best_package_volume' => $methods[0]['volume'],
            'empty_space_percent' => round((($methods[0]['volume'] - $totalCartonsVolume) / $methods[0]['volume']) * 100, 2)
        ]
    ];
}


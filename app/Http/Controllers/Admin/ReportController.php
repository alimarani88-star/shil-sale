<?php

namespace App\Http\Controllers\Admin;

use App\CustomClass\Jdf;
use App\Http\Controllers\Controller;
use App\Models\ExhibitionCustomer;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    public function A_report_of_exhibition_customers(Request $request)
    {
        $exhibition = $request->query('exhibition');
        $city = $request->query('city');

        if($exhibition){
            $customersInfo = ExhibitionCustomer::where('exhibition_name', $exhibition)->get();
        }elseif ($city) {
            $customersInfo = ExhibitionCustomer::where('province_name', $city)->get();
        }
        else{
            $customersInfo = ExhibitionCustomer::all();
        }

        $pageTitle = 'گزارش مشتریان ' ;
        if($exhibition){
            $pageTitle .= $exhibition ;
        }elseif ($city) {
            $pageTitle .= 'بر اساس استان ' ;
            $pageTitle .= $city ;
        }else{
            $pageTitle .= 'نمایشگاه';
        }

        return view('Admin.Reports.A_report_of_exhibition_customers', compact('customersInfo' , 'exhibition' ,'city' , 'pageTitle'));
    }

    public function A_report_of_site_customers()
    {
        $customersInfo = User::with([
            'profile',
            'latestAddress.city'
        ])->get();


        return view('Admin.Reports.A_report_of_site_customers', compact('customersInfo'));
    }

    public function A_report_of_per_customer($id)
    {
        $customerInfo = User::with([
            'profile',
            'latestAddress.city'
        ])->where('id', $id)->first();

        return view('Admin.Reports.A_report_of_per_customer', compact('customerInfo'));
    }

    public function A_report_of_exhibition_visitors()
    {
        $months = [
            1 => 'فروردین',
            2 => 'اردیبهشت',
            3 => 'خرداد',
            4 => 'تیر',
            5 => 'مرداد',
            6 => 'شهریور',
            7 => 'مهر',
            8 => 'آبان',
            9 => 'آذر',
            10 => 'دی',
            11 => 'بهمن',
            12 => 'اسفند',
        ];
        $exhibitionInfos = ExhibitionCustomer::select(
            'exhibition_name',
            'year',
            'month'
        )
            ->selectRaw('COUNT(*) as visit_count')
            ->groupBy('exhibition_name', 'year', 'month')
            ->get();
        $exhibitionInfos->each(function ($item) use ($months) {
            $item->month_name = $months[$item->month] ?? 'نامشخص';
        });
        return view('Admin.Reports.A_report_of_exhibition_visitors' , compact('exhibitionInfos'));
    }

    public function A_report_of_exhibition_visitors_by_city(Request $request)
    {
        $exhibition_by_province=[];
        $year = $request->query('year', 0);
        $query = ExhibitionCustomer::query();

        if ($year != 0) {
            $query->where('year', $year);
        }

        $exhibition_province = $query
            ->get()
            ->groupBy('province_name');



        foreach ($exhibition_province as $province_name => $exhibition_info) {

            $groupedByExhibition = collect($exhibition_info)
                ->groupBy('exhibition_name')->toArray();

            foreach ($groupedByExhibition as $exhibition_name => $customers) {
                $query = ExhibitionCustomer::where('exhibition_name', $exhibition_name);
                if ($year != 0) {
                    $query->where('year', $year);
                }
                $exhibition_name_visit_count = $query->count();


                $exhibition_by_province[$province_name][$exhibition_name] = [
                    'visit'=> count($customers),
                    'percent' => $exhibition_name_visit_count > 0
                        ? round((count($customers) / $exhibition_name_visit_count) * 100, 1)
                        : 0
                ];
            }

        }

        $now_m = date('Y-m-d');
        $jdf = new Jdf();
        $now_j = $jdf->toJalali($now_m);
        list($y,$m,$d)= explode("/",$now_j);
        $years = range(1400, $y);

        return view('Admin.Reports.A_report_of_exhibition_visitors_by_city', compact('exhibition_by_province', 'year'  , 'years'));
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Traits\ReportTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    use ReportTrait;

    public function orders(Request $request)
    {
        $fromDate = $this->getDatesFromTheParam() ?:Carbon::now()->subDay(30);
        // You can now use $fromDate in your query
        $query = Order::query()
        ->select([DB::raw('CAST(created_at as DATE)AS day'), DB::raw('COUNT(id) AS count')])
        ->groupBy(DB::raw('CAST(created_at as DATE)'));
        if ($fromDate) {
            $query->where('created_at', '>', $fromDate);
        }
        $orders = $query->get()->keyBy('day');
        // proccess the chart
        $days = [];
        $now = Carbon::now();
        while ($fromDate < $now){
            $label = $fromDate->format('Y-m-d');
            $labels[] = $label;
            $fromDate = $fromDate->addDay(1);
            $days[] = isset($orders[$label]) ? $orders[$label]['count'] : 0;
        }
        return[
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Orders',
                'backgroundColor'=>'#f87979',
                'data' => $days
            ]]
        ];
    }

    public function customers(Request $request)
    {
        // Implement logic here
    }


    private function fillGaps(\Illuminate\Database\Eloquent\Collection|array $orders)
    {
        //TODO
        return $orders;
    }
}

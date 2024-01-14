<?php

namespace App\Http\Controllers\API\v1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $r)
    {
        $booksCount = \App\Models\Book::count();
        $genresCount = \App\Models\Genre::count();
        $authorsCount = \App\Models\Author::count();
        $customersCount = \App\Models\Customer::count();

        $monthly_data = \App\Models\Order::select(
            \Illuminate\Support\Facades\DB::raw('YEAR(order_date) as year'),
            \Illuminate\Support\Facades\DB::raw('MONTH(order_date) as month'),
            \Illuminate\Support\Facades\DB::raw('SUM(total_amount) as total_amount')
        )
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Initialize arrays for labels and data
        $labels = [];
        $data = [];

        // Populate labels and data arrays
        foreach ($monthly_data as $item) {
            $month = date('M', mktime(0, 0, 0, $item->month, 1)); // Convert month number to abbreviation
            $labels[] = $month;
            $data[] = $item->total_amount;
        }

        // Prepare the final data structure for Chart.js
        $chart_data = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Income per Month',
                    'data' => $data,
                    'backgroundColor' => 'rgb(52, 89, 230, 0.2)',
                    'borderColor' => 'rgb(52, 89, 230, 1)',
                    'borderWidth' => 2,
                ],
            ],
        ];

        return response()->json([
            'message' => 'OK',
            'data' => [
                'booksCount' => $booksCount,
                'genresCount' => $genresCount,
                'authorsCount' => $authorsCount,
                'customersCount' => $customersCount,
                'chart' => $chart_data
            ]
        ]);
    }
}

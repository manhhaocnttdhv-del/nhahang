<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function getAvailable()
    {
        $tables = Table::where('is_active', true)
            ->where('status', 'available')
            ->select('id', 'name', 'number', 'capacity', 'area')
            ->orderBy('area')
            ->orderBy('capacity')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tables,
        ]);
    }
}


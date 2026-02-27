<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesExport implements FromCollection, WithHeadings
{
    protected $sales;

    public function __construct($sales)
    {
        $this->sales = $sales;
    }

    public function collection()
    {
        return $this->sales->map(function ($sale) {
            return [
                'Date' => $sale->created_at->format('d-m-Y'),

                // Products
                'Produits' => $sale->items->map(fn($item) => $item->product->name)->join(', '),

                // Quantities
                'Quantités' => $sale->items->map(fn($item) => $item->quantity)->join(', '),

                // Regions (use region_id if set on sale_item)
                'Régions' => $sale->items->map(function ($item) {
                    if ($item->region_id) {
                        return $item->region->name ?? 'N/A';
                    }
                    // fallback to product stocks
                    $regions = $item->product->stocks->pluck('region.name')->unique();
                    return $regions->join(', ') ?: 'N/A';
                })->join(', '),

                'Total (KMF)' => $sale->grand_total,
            ];
        });
    }

    public function headings(): array
    {
        return ['Date', 'Produits', 'Quantités', 'Régions', 'Total (KMF)'];
    }
}

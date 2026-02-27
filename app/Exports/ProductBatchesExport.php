<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductBatchesExport implements FromCollection, WithHeadings
{
    protected $regionId;
    protected $productId;

    public function __construct($regionId = null, $productId = null)
    {
        $this->regionId  = $regionId;
        $this->productId = $productId;
    }

    public function collection()
    {
        $query = DB::table('product_batches')
            ->join('product_types', 'product_types.id', '=', 'product_batches.product_id')
            ->join('regions', 'regions.id', '=', 'product_batches.region_id')
            ->select(
                'product_types.name as produit',
                'regions.name as region',
                'product_batches.quantity_available as quantite',
                'product_batches.created_at as cree_le'
            );

        if ($this->regionId) {
            $query->where('product_batches.region_id', $this->regionId);
        }

        if ($this->productId) {
            $query->where('product_batches.product_id', $this->productId);
        }

        return $query
            ->orderBy('product_batches.created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Produit',
            'Région',
            'Quantité',
            'Créé le',
        ];
    }
}

<?php

namespace App\Traits\Services;

use Illuminate\Support\Facades\DB;

trait HasReorder
{
    abstract protected function getReorderModel(): string;

    public function reorder(array $ids): void
    {
        $model = $this->getReorderModel();

        $sortOrders = $model::whereIn('id', $ids)
            ->orderBy('sort_order')
            ->pluck('sort_order')
            ->toArray();

        DB::transaction(function () use ($model, $ids, $sortOrders) {
            foreach ($ids as $i => $id) {
                $model::where('id', $id)->update(['sort_order' => $sortOrders[$i]]);
            }
        });
    }
}

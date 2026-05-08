<?php

namespace App\Traits\Services;

trait HasBulkDelete
{
    public function bulkDelete(array $ids): void
    {
        foreach ($ids as $id) {
            $this->delete($id);
        }
    }
}

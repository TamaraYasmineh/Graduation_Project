<?php

namespace App\Services;

use App\Models\Drug;

class DrugService
{
    /**
     * إضافة دواء
     */
    public function create(array $data)
    {
        return Drug::create([
            'protocol_id' => $data['protocol_id'],
            'name' => $data['name'],
            'dose' => $data['dose'],
            'dose_basis' => $data['dose_basis'],
            'route' => $data['route'],
        ]);
    }

    /**
     * تعديل دواء
     */
    public function update(Drug $drug, array $data)
    {
        $drug->update([
            'protocol_id' => $data['protocol_id'] ?? $drug->protocol_id,
            'name' => $data['name'] ?? $drug->name,
            'dose' => $data['dose'] ?? $drug->dose,
            'dose_basis' => $data['dose_basis'] ?? $drug->dose_basis,
            'route' => $data['route'] ?? $drug->route,
        ]);

        return $drug;
    }

    /**
     * حذف دواء
     */
    public function delete(Drug $drug)
    {
        return $drug->delete();
    }
}

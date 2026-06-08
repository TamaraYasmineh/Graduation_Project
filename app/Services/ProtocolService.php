<?php

namespace App\Services;

use App\Models\Protocol;

class ProtocolService
{
    /**
     * إنشاء بروتوكول
     */
    public function create(array $data)
    {
        return Protocol::create([
            'name' => $data['name'],

            'disease_type' => $data['disease_type'] ?? null,
            'therapeutic_intent' => $data['therapeutic_intent'] ?? null,

            'cycle_length_days' => $data['cycle_length_days'] ?? null,
            'administration_days' => $data['administration_days'] ?? null,
            'suggested_number_of_cycles' => $data['suggested_number_of_cycles'] ?? null,

            'pre_medications' => $data['pre_medications'] ?? null,
            'mandatory_tests' => $data['mandatory_tests'] ?? null,

        ]);
    }

    /**
     * تحديث بروتوكول
     */
    public function update(Protocol $protocol, array $data)
    {
        $protocol->update([
            'name' => $data['name'] ?? $protocol->name,

            'disease_type' => $data['disease_type'] ?? $protocol->disease_type,
            'therapeutic_intent' => $data['therapeutic_intent'] ?? $protocol->therapeutic_intent,

            'cycle_length_days' => $data['cycle_length_days'] ?? $protocol->cycle_length_days,
            'administration_days' => $data['administration_days'] ?? $protocol->administration_days,
            'suggested_number_of_cycles' => $data['suggested_number_of_cycles'] ?? $protocol->suggested_number_of_cycles,

            'pre_medications' => $data['pre_medications'] ?? $protocol->pre_medications,
            'mandatory_tests' => $data['mandatory_tests'] ?? $protocol->mandatory_tests,

        ]);

        return $protocol;
    }

    /**
     * حذف بروتوكول
     */
    public function delete(Protocol $protocol)
    {
        return $protocol->delete();
    }
}

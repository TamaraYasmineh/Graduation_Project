<?php

namespace App\Http\Controllers;

use App\Models\Drug;
use App\Services\DrugService;
use Illuminate\Http\Request;

class DrugController extends BaseController
{
    /**
     * عرض كل الأدوية
     */
    public function index()
    {
        $drugs = Drug::with('protocol')->get();

        return $this->sendResponse(
            $drugs,
            'Drugs retrieved successfully'
        );
    }

    /**
     * إضافة دواء
     */
    public function store(Request $request, DrugService $service)
    {
        $drug = $service->create($request->all());

        return $this->sendResponse(
            $drug,
            'Drug created successfully',
            201
        );
    }

    /**
     * عرض دواء
     */
    public function show($id)
    {
        $drug = Drug::with('protocol')->findOrFail($id);

        return $this->sendResponse(
            $drug,
            'Drug retrieved successfully'
        );
    }

    /**
     * تعديل دواء
     */
    public function update(Request $request, $id, DrugService $service)
    {
        $drug = Drug::findOrFail($id);

        $updated = $service->update($drug, $request->all());

        return $this->sendResponse(
            $updated,
            'Drug updated successfully'
        );
    }

    /**
     * حذف دواء
     */
    public function destroy($id, DrugService $service)
    {
        $drug = Drug::findOrFail($id);

        $service->delete($drug);

        return $this->sendResponse(
            null,
            'Drug deleted successfully'
        );
    }
}
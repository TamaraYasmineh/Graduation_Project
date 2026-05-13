<?php

namespace App\Http\Controllers;

use App\Models\Protocol;
use App\Services\ProtocolService;
use Illuminate\Http\Request;

class ProtocolController extends BaseController
{
    /**
     * عرض جميع البروتوكولات
     */
    public function index()
    {
        $protocols = Protocol::all();

        return $this->sendResponse(
            $protocols,
            'Protocols retrieved successfully'
        );
    }

    /**
     * إضافة بروتوكول
     */
    public function store(Request $request, ProtocolService $service)
    {
        $protocol = $service->create($request->all());

        return $this->sendResponse(
            $protocol,
            'Protocol created successfully',
            201
        );
    }

    /**
     * عرض بروتوكول واحد
     */
    public function show($id)
    {
        $protocol = Protocol::findOrFail($id);

        return $this->sendResponse(
            $protocol,
            'Protocol retrieved successfully'
        );
    }

    /**
     * تعديل بروتوكول
     */
    public function update(Request $request, $id, ProtocolService $service)
    {
        $protocol = Protocol::findOrFail($id);

        $updated = $service->update($protocol, $request->all());

        return $this->sendResponse(
            $updated,
            'Protocol updated successfully'
        );
    }

    /**
     * حذف بروتوكول
     */
    public function destroy($id, ProtocolService $service)
    {
        $protocol = Protocol::findOrFail($id);

        $service->delete($protocol);

        return $this->sendResponse(
            null,
            'Protocol deleted successfully'
        );
    }
    public function showAllProtocolwithDrugs()
{
    $protocols = Protocol::with('drugs')->get();

    return $this->sendResponse(
        $protocols,
        'Protocols retrieved successfully'
    );
}
public function showProtocolwithDrugs($id)
{
    $protocol = Protocol::with('drugs')->findOrFail($id);

    return $this->sendResponse(
        $protocol,
        'Protocol retrieved successfully'
    );
}
}
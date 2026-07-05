<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostChemoRecommendationRequest;
use App\Http\Requests\UpdatePostChemoRecommendationRequest;
use App\Http\Resources\PostChemoRecommendationResource;
use App\Models\PostChemoRecommendation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostChemoRecommendationController extends Controller
{
    /**
     * جلب التوصيات فقط
     * GET /api/recommendations
     */
    public function recommendations(): JsonResponse
    {
        $data = PostChemoRecommendation::active()
            ->ofType('recommendation')
            ->get();

        return response()->json([
            'success' => true,
            'data' => PostChemoRecommendationResource::collection($data),
        ]);
    }

    /**
     * جلب الأعراض التحذيرية فقط
     * GET /api/warning-symptoms
     */
    public function warningSymptoms(): JsonResponse
    {
        $data = PostChemoRecommendation::active()
            ->ofType('warning_symptom')
            ->get();

        return response()->json([
            'success' => true,
            'data' => PostChemoRecommendationResource::collection($data),
        ]);
    }

    /**
     * تعديل عنصر (توصية أو عرض) - للطبيب/الأدمن فقط
     * PUT /api/recommendations/{recommendation}
     */
    public function updateRecommendations(
        UpdatePostChemoRecommendationRequest $request,
        PostChemoRecommendation $recommendation
    ): JsonResponse {
        $recommendation->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث العنصر بنجاح',
            'data' => new PostChemoRecommendationResource($recommendation),
        ]);
    }

    /**
     * جلب كل العناصر (نشطة وغير نشطة) - للسوبر أدمن فقط
     * GET /api/admin/recommendations
     */
    public function allRecommendationsForAdmin(): JsonResponse
    {
        $data = PostChemoRecommendation::orderBy('type')
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'total' => $data->count(),
            'active_count' => $data->where('is_active', true)->count(),
            'inactive_count' => $data->where('is_active', false)->count(),
            'data' => PostChemoRecommendationResource::collection($data),
        ]);
    }
    /**
     * إضافة عنصر جديد (توصية أو عرض تحذيري) - للسوبر أدمن
     * POST /api/admin/recommendations
     */
    public function storeRecommendations(StorePostChemoRecommendationRequest $request): JsonResponse
    {
        $recommendation = PostChemoRecommendation::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'تمت الإضافة بنجاح',
            'data' => new PostChemoRecommendationResource($recommendation),
        ], 201);
    }
    /**
     * حذف عنصر حسب الـ id - للسوبر أدمن
     * DELETE /api/admin/recommendations/{recommendation}
     */
    public function destroyRecommendations(PostChemoRecommendation $recommendation): JsonResponse
    {
        $recommendation->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف العنصر بنجاح',
        ]);
    }
}

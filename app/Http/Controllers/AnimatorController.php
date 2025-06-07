<?php

namespace App\Http\Controllers;

use App\Models\Animator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnimatorController extends Controller
{
    /**
     * Сохранение черновика (AJAX)
     */
    public function saveDraft(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $data = $this->prepareAnimatorData($request);
            
            if ($draftId = $request->input('draft_id')) {
                $animator = Animator::where('id', $draftId)
                    ->where('user_id', auth()->id())
                    ->where('status', 'draft')
                    ->first();
                
                if ($animator) {
                    $animator->update($data);
                } else {
                    $animator = Animator::create($data);
                }
            } else {
                $animator = Animator::create($data);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'animator' => $animator,
                'message' => 'Черновик сохранен'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ошибка сохранения черновика', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка сохранения'
            ], 500);
        }
    }
    
    /**
     * Подготовка данных для сохранения
     */
    private function prepareAnimatorData(Request $request): array
    {
        return [
            'user_id' => auth()->id(),
            'status' => $request->input('status', 'draft'),
            
            // Основные данные с значениями по умолчанию
            'title' => $request->input('details.title', ''),
            'name' => $request->input('details.title', ''),
            'description' => $request->input('details.description', ''),
            
            // JSON поля
            'work_format' => $request->input('workFormat', []),
            'price_list' => $request->input('priceList', ['priceItems' => []]),
            'actions_data' => $request->input('actions', []),
            'geo_data' => $request->input('geo', []),
            'contacts_data' => $request->input('contacts', []),
            
            // Обязательные поля с дефолтными значениями
            'type' => $request->input('workFormat.type', 'private'),
            'city' => $request->input('geo.city', 'Москва'),
            'price' => $request->input('price.value', 0),
            
            // Опциональные поля
            'address' => $request->input('geo.address', ''),
            'phone' => $request->input('contacts.phone', ''),
            'email' => $request->input('contacts.email', ''),
            'specialization' => $request->input('workFormat.specialization', ''),
            
            // Флаги
            'quick_booking' => false,
            'terms_accepted' => true,
        ];
    }
}
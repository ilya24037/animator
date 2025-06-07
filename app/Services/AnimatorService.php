<?php

namespace App\Services;

use App\Models\Animator;
use App\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AnimatorService
{
    public static function create(array $data, $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $data['user_id'] = $user->id;
            $animator = Animator::create($data);

            if (isset($data['media_files'])) {
                self::handleMedia($data['media_files'], $animator);
            }
            return $animator;
        });
    }

    public static function update(Animator $animator, array $data, $user)
    {
        return DB::transaction(function () use ($animator, $data) {
            $animator->update($data);

            if (isset($data['media_files'])) {
                self::handleMedia($data['media_files'], $animator);
            }
            return $animator;
        });
    }

    public static function destroy(Animator $animator)
    {
        // Можно реализовать SoftDelete
        $animator->delete();
    }

    public static function handleMedia($files, Animator $animator)
    {
        foreach ($files as $file) {
            $path = $file->store('animators/' . $animator->id, 'public');
            Media::create([
                'animator_id' => $animator->id,
                'path' => $path,
                'type' => 'photo',
                'uuid' => Str::uuid(),
            ]);
        }
    }

    public static function getUserDraft($userId)
    {
        return Animator::where('user_id', $userId)
            ->where('status', 'draft')
            ->latest()
            ->first();
    }

    public static function formatDraftData(Animator $animator)
    {
        return [
            'id' => $animator->id,
            'details' => [
                'title' => $animator->title,
                'description' => $animator->description
            ],
            // ...остальные поля из вашего formatDraftData
        ];
    }
}

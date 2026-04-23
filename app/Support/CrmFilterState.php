<?php

namespace App\Support;

use Illuminate\Http\Request;

class CrmFilterState
{
    public static function restore(Request $request, string $sessionKey, array $allowed): array
    {
        if ($request->boolean('reset_filters')) {
            $request->session()->forget($sessionKey);

            return [
                'values' => [],
                'redirect' => false,
            ];
        }

        $hasExplicitFilters = collect($allowed)->contains(function (string $key) use ($request): bool {
            if (in_array($key, ['my_leads', 'unassigned_only'], true)) {
                return $request->has($key);
            }

            return $request->filled($key);
        });

        if ($hasExplicitFilters) {
            $values = self::extract($request, $allowed);
            $request->session()->put($sessionKey, $values);

            return [
                'values' => $values,
                'redirect' => false,
            ];
        }

        if ($request->query->count() === 0 && $request->session()->has($sessionKey)) {
            return [
                'values' => (array) $request->session()->get($sessionKey, []),
                'redirect' => true,
            ];
        }

        return [
            'values' => self::extract($request, $allowed),
            'redirect' => false,
        ];
    }

    private static function extract(Request $request, array $allowed): array
    {
        return collect($allowed)
            ->mapWithKeys(function (string $key) use ($request): array {
                if (in_array($key, ['my_leads', 'unassigned_only'], true)) {
                    return $request->has($key) ? [$key => $request->boolean($key)] : [];
                }

                $value = $request->input($key);

                return filled($value) ? [$key => $value] : [];
            })
            ->all();
    }
}

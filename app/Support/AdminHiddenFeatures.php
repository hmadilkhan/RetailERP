<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Temporary: Admin (roleId 1) ke login pe Billing Section, Impersonate (Login As), WhatsApp Access aur Users list ka Password column chhupana.
 * Wapas laane ke liye .env me HIDE_ADMIN_BILLING_IMPERSONATE=false (ya line hata do) aur `php artisan config:clear`.
 */
class AdminHiddenFeatures
{
    public static function active(): bool
    {
        return (bool) config('app.hide_admin_billing_impersonate', false)
            && (int) session('roleId') === 1;
    }

    /**
     * Sidebar pages se Billing Section (aur uske child pages) hatao.
     */
    public static function filterSidebarPages(Collection $pages): Collection
    {
        if (!self::active()) {
            return $pages;
        }

        $hidden = $pages
            ->filter(fn ($page) => self::isBillingPage($page))
            ->pluck('id')
            ->all();

        // children (kisi bhi gehrai tak)
        do {
            $before = count($hidden);
            foreach ($pages as $page) {
                if (in_array($page->parent_id, $hidden, true) && !in_array($page->id, $hidden, true)) {
                    $hidden[] = $page->id;
                }
            }
        } while (count($hidden) !== $before);

        return $pages->reject(fn ($page) => in_array($page->id, $hidden, true))->values();
    }

    private static function isBillingPage($page): bool
    {
        $url = trim((string) $page->page_url, '/');

        return \Illuminate\Support\Str::snake(strtolower((string) $page->page_name)) === 'billing_section'
            || str_starts_with($url, 'billing/')
            || $url === 'billing'
            || str_starts_with($url, 'invoice-setup');
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * After every admin request that results in a redirect, this middleware
 * appends the current tab's `_tab` identifier to the redirect URL so that
 * server-side redirects (e.g. after store/update/destroy) carry the tab ID
 * forward automatically — without requiring a second JavaScript redirect.
 */
class InjectTabId
{
    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        $tab = substr(preg_replace('/[^a-zA-Z0-9\-]/', '', $request->input('_tab', '')), 0, 40);

        if ($tab !== '' && $response instanceof RedirectResponse) {
            $target = $response->getTargetUrl();

            // Only inject if not already present
            if (!str_contains($target, '_tab=')) {
                $separator = str_contains($target, '?') ? '&' : '?';
                $response->setTargetUrl($target . $separator . '_tab=' . $tab);
            }
        }

        return $response;
    }
}

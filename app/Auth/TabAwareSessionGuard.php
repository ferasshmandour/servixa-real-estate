<?php

namespace App\Auth;

use Illuminate\Auth\SessionGuard;

/**
 * A session guard that stores each browser tab's admin session under a
 * separate key, allowing multiple different admin accounts to be logged in
 * simultaneously in different tabs of the same browser.
 *
 * Every request must carry a `_tab` query/form parameter (a short random
 * string generated once per tab by client-side JavaScript and persisted in
 * sessionStorage). The guard uses this value to namespace the session key,
 * so Tab A's login never overwrites Tab B's login.
 */
class TabAwareSessionGuard extends SessionGuard
{
    protected function tabId(): string
    {
        $raw = request()->input('_tab', '');
        // Allow only alphanumeric + hyphen, max 40 chars — prevents session-key injection.
        return substr(preg_replace('/[^a-zA-Z0-9\-]/', '', $raw), 0, 40);
    }

    public function getName(): string
    {
        $tab = $this->tabId();
        if ($tab === '') {
            return parent::getName();
        }
        return 'login_' . $this->name . '_tab_' . $tab;
    }

    public function getRecallerName(): string
    {
        $tab = $this->tabId();
        if ($tab === '') {
            return parent::getRecallerName();
        }
        return 'remember_' . $this->name . '_tab_' . $tab;
    }
}

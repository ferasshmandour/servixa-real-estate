# Pusher Setup — Servixa Real-time Chat

The chat code is already wired for Pusher private channels. To turn on real-time delivery you only need to (1) create a Pusher Channels app, (2) paste the keys into `.env`, and (3) flip one switch and rebuild.

Until you do this, chat still **works** (messages send/receive on page load) — they just don't appear *live*. Broadcasts are written to `storage/logs/laravel.log` instead of Pusher (the safe `log` driver default), so nothing errors before keys exist.

---

## 1. Create a Pusher Channels app

1. Go to **https://dashboard.pusher.com** and sign in (free "Sandbox" plan is enough: 100 concurrent connections, 200k messages/day).
2. In the left sidebar choose **Channels** → **Create app**.
   - ⚠️ Pick **Channels**, NOT **Beams**. Beams is mobile push; we need Channels (WebSockets).
3. Name it e.g. `servixa-chat`.
4. **Pick a cluster** close to your users — `eu` (Europe/Middle East) is a good default. Note the slug; it must match `PUSHER_APP_CLUSTER`.
5. (Front-end "Vanilla JS" / back-end "Laravel" only changes which snippets it shows — pick anything.)

## 2. Copy the keys into `.env`

Open the app's **App Keys** tab and copy the four values into `.env`:

```env
PUSHER_APP_ID=1234567
PUSHER_APP_KEY=abcd1234abcd1234abcd
PUSHER_APP_SECRET=secret1234secret1234       # server-side only — never exposed to the browser
PUSHER_APP_CLUSTER=eu                          # must match the cluster you chose
```

The browser-side `VITE_PUSHER_*` keys already reference these in `.env`, so you don't retype them:

```env
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
```

## 3. Activate Pusher and rebuild

```bash
# 1) Flip the broadcaster from "log" to "pusher" in .env:
#    BROADCAST_CONNECTION=pusher

# 2) Clear caches so the new .env/config is picked up (mandatory per CLAUDE.md)
php artisan optimize:clear

# 3) Rebuild assets so the VITE_PUSHER_* values are embedded in the JS bundle
npm run build        # or `npm run dev` while developing

# 4) (production) re-cache
php artisan optimize && php artisan view:cache
```

That's it. The chat thread page subscribes to `private-conversations.{id}` and listens for the `.message.sent` event; new messages now arrive live.

---

## How to test real-time

1. Run the stack: `composer dev` (serves the app + queue + Vite).
2. Open the **Debug Console** tab in the Pusher dashboard.
3. Log in as two different users in two browsers (one normal, one incognito):
   - `http://localhost:8000/chat/login` — phone + password (same credentials as the Servixa app).
4. As user A: **Browse services** → pick a service owned by user B → choose "Act as" (yourself or one of your approved business accounts) → **Chat**.
5. Open the same conversation as user B. Send a message from A → it appears in B's window within ~1 second, and the Debug Console shows `message.sent` on `private-conversations.{id}`.

---

## Notes & troubleshooting

- **Participants only.** `routes/channels.php` authorizes `private-conversations.{id}` only for the conversation's two participants, so outsiders get a 403 from `/broadcasting/auth`.
- **user ↔ user is impossible.** The receiver is always the service owner, who holds an approved business account — so every chat is business↔business or business↔user. The "Act as" picker records which business account the initiator is using (`conversations.initiator_business_account_id`).
- **Synchronous broadcast.** `MessageSent` is `ShouldBroadcastNow`, so it broadcasts in-request regardless of the queue. (FCM push for the message still goes through the queue — run `php artisan queue:work` for those.)
- **419 on send / channel auth?** The chat layout includes `<meta name="csrf-token">` and Echo sends it as `X-CSRF-TOKEN`. If you customized the layout, keep that meta tag.
- **Nothing live, no errors?** Make sure you actually set `BROADCAST_CONNECTION=pusher`, ran `php artisan optimize:clear`, and re-ran `npm run build` (the browser key is baked in at build time).
- **Self-hosted alternative.** To use **Laravel Reverb** instead of Pusher later, install Reverb and set `BROADCAST_CONNECTION=reverb` + the `REVERB_*`/`VITE_REVERB_*` keys — no controller/event/JS changes needed (Echo already uses the `pusher` protocol that Reverb speaks).

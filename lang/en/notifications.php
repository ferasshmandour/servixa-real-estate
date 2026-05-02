<?php

return [

    'business_account_submitted' => [
        'title' => 'New business account pending review',
        'body'  => ':user submitted a business account (:license).',
    ],
    'business_account_approved' => [
        'title' => 'Your business account is approved',
        'body'  => 'Your business account ":name" has been approved. You can now post and request services.',
    ],
    'business_account_rejected' => [
        'title' => 'Your business account was rejected',
        'body'  => 'Reason: :reason',
    ],

    'service_submitted' => [
        'title' => 'New service pending review',
        'body'  => ':user submitted ":title" for review.',
    ],
    'service_resubmitted' => [
        'title' => 'Service edited & needs re-review',
        'body'  => ':user edited ":title" — please review again.',
    ],
    'service_approved' => [
        'title' => 'Your service is approved',
        'body'  => 'Your service ":title" has been approved and is now visible.',
    ],
    'service_rejected' => [
        'title' => 'Your service was rejected',
        'body'  => 'Reason: :reason',
    ],

    'order_received' => [
        'title' => 'New order received',
        'body'  => ':user requested ":title" — quantity :quantity.',
    ],
    'order_accepted' => [
        'title' => 'Your order was accepted',
        'body'  => 'Your order for ":title" has been accepted.',
    ],
    'order_rejected' => [
        'title' => 'Your order was rejected',
        'body'  => 'Your order for ":title" has been rejected.',
    ],

    'rating_added' => [
        'title' => 'You received a new rating',
        'body'  => ':user rated your service ":title" with :rating stars.',
    ],

    'service_reported' => [
        'title' => 'Service reported',
        'body'  => 'Service ":title" was reported. Reason: :reason',
    ],

    'new_message' => [
        'title' => 'New message from :sender',
        'body'  => ':preview',
    ],

];

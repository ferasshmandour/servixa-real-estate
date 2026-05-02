<?php

return [

    'business_account_submitted' => [
        'title' => 'حساب أعمال جديد بانتظار المراجعة',
        'body'  => 'قام :user بإرسال حساب أعمال (:license).',
    ],
    'business_account_approved' => [
        'title' => 'تم قبول حساب أعمالك',
        'body'  => 'تمت الموافقة على حساب أعمالك ":name". يمكنك الآن إضافة الخدمات وطلبها.',
    ],
    'business_account_rejected' => [
        'title' => 'تم رفض حساب أعمالك',
        'body'  => 'السبب: :reason',
    ],

    'service_submitted' => [
        'title' => 'خدمة جديدة بانتظار المراجعة',
        'body'  => 'قام :user بإرسال ":title" للمراجعة.',
    ],
    'service_resubmitted' => [
        'title' => 'خدمة معدّلة بحاجة لإعادة مراجعة',
        'body'  => 'قام :user بتعديل ":title" — يرجى إعادة المراجعة.',
    ],
    'service_approved' => [
        'title' => 'تمت الموافقة على خدمتك',
        'body'  => 'تمت الموافقة على خدمتك ":title" وأصبحت ظاهرة الآن.',
    ],
    'service_rejected' => [
        'title' => 'تم رفض خدمتك',
        'body'  => 'السبب: :reason',
    ],

    'order_received' => [
        'title' => 'طلب جديد',
        'body'  => 'قام :user بطلب ":title" — الكمية :quantity.',
    ],
    'order_accepted' => [
        'title' => 'تم قبول طلبك',
        'body'  => 'تم قبول طلبك للخدمة ":title".',
    ],
    'order_rejected' => [
        'title' => 'تم رفض طلبك',
        'body'  => 'تم رفض طلبك للخدمة ":title".',
    ],

    'rating_added' => [
        'title' => 'حصلت على تقييم جديد',
        'body'  => 'قام :user بتقييم خدمتك ":title" بـ :rating نجوم.',
    ],

    'service_reported' => [
        'title' => 'تم الإبلاغ عن خدمة',
        'body'  => 'تم الإبلاغ عن الخدمة ":title". السبب: :reason',
    ],

    'new_message' => [
        'title' => 'رسالة جديدة من :sender',
        'body'  => ':preview',
    ],

];

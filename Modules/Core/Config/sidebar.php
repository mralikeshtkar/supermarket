<?php

use Modules\Permission\Enums\Permissions;

return [
    [
        "title" => "پیشخوان",
        "icon" => "simple-icon-speedometer",
        "href" => [
            "name" => "index",
        ],
    ],
    [
        "title" => "محصولات",
        "icon" => "simple-icon-social-dropbox",
        "permissions" => [
            Permissions::MANAGE_PRODUCTS,
            Permissions::MANAGE_PRODUCT_UNITS,
            Permissions::MANAGE_SPECIAL_PRODUCTS,
        ],
        "submenus" => [
            [
                "title" => "مدیریت محصولات",
                "icon" => "simple-icon-drawer",
                "permissions" => [
                    Permissions::MANAGE_PRODUCTS,
                ],
                "href" => [
                    "name" => "products.index",
                ],
            ],
            [
                "title" => "ثبت محصول",
                "icon" => "simple-icon-plus",
                "permissions" => [
                    Permissions::MANAGE_PRODUCTS,
                ],
                "href" => [
                    "name" => "products.create",
                ],
            ],
            [
                "title" => "مدیریت واحدهای محصول",
                "icon" => "simple-icon-pie-chart",
                "permissions" => [
                    Permissions::MANAGE_PRODUCT_UNITS,
                ],
                "href" => [
                    "name" => "product-units.index",
                ],
            ],
            [
                "title" => "محصولات ویژه",
                "icon" => "simple-icon-diamond",
                "permissions" => [
                    Permissions::MANAGE_SPECIAL_PRODUCTS,
                ],
                "href" => [
                    "name" => "special-products.index",
                ],
            ],
        ]
    ],
    [
        "title" => "سفارشات",
        "icon" => "simple-icon-bag",
        "permissions" => [
            Permissions::MANAGE_ORDERS,
        ],
        "href" => [
            "name" => "orders.index",
        ],
    ],
    [
        "title" => "برند",
        "icon" => "simple-icon-badge",
        "permissions" => [
            Permissions::MANAGE_PERMISSIONS,
        ],
        "submenus" => [
            [
                "title" => "مدیریت برندها",
                "icon" => "simple-icon-drawer",
                "href" => [
                    "name" => "brands.index",
                ],
            ],
            [
                "title" => "ثبت برند",
                "icon" => "simple-icon-plus",
                "href" => [
                    "name" => "brands.create",
                ],
            ],
        ]
    ],
    [
        "title" => "کاربران",
        "icon" => "simple-icon-people",
        "permissions" => [
            Permissions::MANAGE_USERS,
        ],
        "submenus" => [
            [
                "title" => "مدیریت کاربران",
                "icon" => "simple-icon-drawer",
                "href" => [
                    "name" => "users.index",
                ],
            ],
            [
                "title" => "ثبت کاربر",
                "icon" => "simple-icon-plus",
                "href" => [
                    "name" => "users.create",
                ],
            ],
            [
                "title" => "کاربران آنلاین",
                "icon" => "simple-icon-star",
                "href" => [
                    "name" => "users.online.index",
                ],
            ],
        ]
    ],
    [
        "title" => "نظرات",
        "icon" => "simple-icon-bubbles",
        "permissions" => [
            Permissions::MANAGE_COMMENTS,
        ],
        "href" => [
            "name" => "comments.index",
        ],
    ],
    [
        "title" => "تبلیغات",
        "icon" => "simple-icon-magic-wand",
        "permissions" => [
            Permissions::MANAGE_ADVERTISEMENTS,
        ],
        "href" => [
            "name" => "advertisements.index",
        ],
    ],
    [
        "title" => "پوسترها",
        "icon" => "simple-icon-social-foursqare",
        "permissions" => [
            Permissions::MANAGE_POSTERS,
        ],
        "href" => [
            "name" => "posters.index",
        ],
    ],
    [
        "title" => "نظرسنجی ها",
        "icon" => "simple-icon-chart",
        "permissions" => [
            Permissions::MANAGE_POSTERS,
        ],
        "href" => [
            "name" => "votes.index",
        ],
    ],
    [
        "title" => "اخبار",
        "icon" => "simple-icon-feed",
        "permissions" => [
            Permissions::MANAGE_NEWS,
        ],
        "submenus" => [
            [
                "title" => "مدیریت اخبار",
                "icon" => "simple-icon-drawer",
                "href" => [
                    "name" => "news.index",
                ],
            ],
            [
                "title" => "دسته های اخبار",
                "icon" => "simple-icon-star",
                "href" => [
                    "name" => "news-categories.index",
                ],
            ],
        ],
    ],
    [
        "title" => "نقش‌های کاربری",
        "icon" => "simple-icon-directions",
        "permissions" => [
            Permissions::MANAGE_PERMISSIONS,
        ],
        "submenus" => [
            [
                "title" => "مدیریت نقش‌های کاربری",
                "icon" => "simple-icon-drawer",
                "href" => [
                    "name" => "roles.index",
                ],
            ],
            [
                "title" => "ثبت نقش‌ کاربری",
                "icon" => "simple-icon-plus",
                "href" => [
                    "name" => "roles.create",
                ],
            ],
        ]
    ],
    [
        "title" => "دسته بندی‌ها",
        "icon" => "simple-icon-organization",
        "permissions" => [
            Permissions::MANAGE_CATEGORIES,
        ],
        "submenus" => [
            [
                "title" => "مدیریت دسته بندی‌ها",
                "icon" => "simple-icon-drawer",
                "href" => [
                    "name" => "categories.index",
                ],
            ],
            [
                "title" => "ثبت دسته بندی‌",
                "icon" => "simple-icon-plus",
                "href" => [
                    "name" => "categories.create",
                ],
            ],
        ]
    ],
    [
        "title" => "تخفیف‌ها",
        "icon" => "simple-icon-present",
        "permissions" => [
            Permissions::MANAGE_DISCOUNTS,
        ],
        "href" => [
            "name" => "discounts.index",
        ],
    ],
    [
        "title" => "برچسب‌ها",
        "icon" => "simple-icon-paper-clip",
        "permissions" => [
            Permissions::MANAGE_TAGS,
        ],
        "submenus" => [
            [
                "title" => "مدیریت برچسب‌ها",
                "icon" => "simple-icon-drawer",
                "href" => [
                    "name" => "tags.index",
                ],
            ],
            [
                "title" => "ثبت برچسب‌",
                "icon" => "simple-icon-plus",
                "href" => [
                    "name" => "tags.create",
                ],
            ],
        ]
    ],
    [
        "title" => "قفسه‌ها",
        "icon" => "simple-icon-layers",
        "permissions" => [
            Permissions::MANAGE_RACKS,
        ],
        "href" => [
            "name" => "racks.index",
        ],
    ],
    [
        "title" => "انبارها",
        "icon" => "simple-icon-share-alt",
        "permissions" => [
            Permissions::MANAGE_STOREROOMS,
        ],
        "submenus" => [
            [
                "title" => "مدیریت انبارها",
                "icon" => "simple-icon-drawer",
                "href" => [
                    "name" => "storerooms.index",
                ],
            ],
            [
                "title" => "لیست موجودی محصولات",
                "icon" => "simple-icon-umbrella",
                "href" => [
                    "name" => "storeroom.products.stock.index",
                ],
            ],
        ]
    ],
    [
        "title" => "گزارش فعالیت مدیران",
        "icon" => "simple-icon-calendar",
        "permissions" => [
            Permissions::MANAGE_LOG_ACTIVITIES,
        ],
        "href" => [
            "name" => "activities.index",
        ],
    ],
    [
        "title" => "تنظیمات",
        "permissions" => [
            Permissions::MANAGE_SETTINGS,
        ],
        "icon" => "simple-icon-settings",
        "href" => [
            "name" => "settings.index",
        ],
    ],
];

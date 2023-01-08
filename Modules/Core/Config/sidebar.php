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
            Permissions::MANAGE_PERMISSIONS,
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
            Permissions::MANAGE_PERMISSIONS,
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
        "href" => [
            "name" => "comments.index",
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
            Permissions::MANAGE_PERMISSIONS,
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
        "href" => [
            "name" => "discounts.index",
        ],
    ],
    [
        "title" => "برچسب‌ها",
        "icon" => "simple-icon-paper-clip",
        "permissions" => [
            Permissions::MANAGE_PERMISSIONS,
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
        "href" => [
            "name" => "racks.index",
        ],
    ],
    [
        "title" => "انبارها",
        "icon" => "simple-icon-share-alt",
        "permissions" => [
            Permissions::MANAGE_PERMISSIONS,
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
        "href" => [
            "name" => "activities.index",
        ],
    ],
    [
        "title" => "تنظیمات",
        "icon" => "simple-icon-settings",
        "href" => [
            "name" => "settings.index",
        ],
    ],
];

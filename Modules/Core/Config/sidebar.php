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
];

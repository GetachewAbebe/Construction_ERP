<?php

return [
    /**
     * Default component prefix.
     *
     * Set to 'mary-' so the FULL Mary UI component set is namespaced
     * (e.g. <x-mary-select />, <x-mary-toast />) and cannot clash with the
     * application's own Blade components.
     */
    'prefix' => 'mary-',

    /**
     * Default route prefix for Mary's internal routes (spotlight, editor, …).
     */
    'route_prefix' => '',

    /**
     * Components settings
     */
    'components' => [
        'spotlight' => [
            'class' => 'App\Support\Spotlight',
        ],
    ],
];

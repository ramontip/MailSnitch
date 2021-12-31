<?php

/**
 * MailSnitch
 */
return [
    /**
     * Main Config
     */
    'main' => [
        'user_agent_lang'   =>      'en-En,en',
        'mail_from'         =>      '',
        'mail_to' => [
            '',
        ]
    ],

    /**
     * Register Actions
     */
    'actions' => [
        /**
         * Example Action
         * Parse online shops and check if sneaker is available for purchase.
         */
        [
            /**
             * Parameters
             * Insert details for action.
             */
            'key'           =>      'nike',
            'title'         =>      'Check Nike Air Jordan 1 availability',
            'success'       =>      'Nike Air Jordan 1 is available',
            'error'         =>      'not available',

            /**
             * Checks
             * Define urls, phrases and if they should be contained on the page or not.
             */
            'checks' => [
                ['https://first-shop.example/product/nike-air-jordan-1/', 'Add to Basket', true],
                ['https://second-shop.example/product/x3134fw/nike-air-jordan-1/', 'Coming Soon', false],
            ]
        ],
    ],
];

?>
<?php
return [
    'frontend' => [
        'asynai/chatbot-injector' => [
            'target' => \Asyntai\Chatbot\Middleware\InjectWidgetMiddleware::class,
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ],
        ],
    ],
];



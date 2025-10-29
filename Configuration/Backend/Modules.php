<?php
use Asyntai\Chatbot\Controller\ModuleController;

return [
    'tools_AsyntaiChatbot' => [
        'parent' => 'tools',
        'position' => ['after' => 'tools_ExtensionmanagerExtensionmanager'],
        'access' => 'admin',
        'workspaces' => 'live',
        'path' => '/module/tools/asyntai',
        'iconIdentifier' => 'asyntai-chatbot-icon',
        'labels' => 'LLL:EXT:asyntai_chatbot/Resources/Private/Language/locallang_mod.xlf',
        'routes' => [
            '_default' => [
                'target' => ModuleController::class . '::indexAction',
            ],
        ],
        'moduleData' => [
            'session' => [
                'sessions',
            ],
        ],
    ],
];



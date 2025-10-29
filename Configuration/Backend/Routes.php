<?php
use TYPO3\CMS\Core\Authentication\Mfa\MfaRequiredVerdict;

return [
    'asyntai_chatbot_save' => [
        'path' => '/asyntai/save',
        'target' => \Asyntai\Chatbot\Controller\AjaxController::class . '::saveAction',
        'methods' => ['POST'],
    ],
    'asyntai_chatbot_reset' => [
        'path' => '/asyntai/reset',
        'target' => \Asyntai\Chatbot\Controller\AjaxController::class . '::resetAction',
        'methods' => ['POST'],
    ],
];


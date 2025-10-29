<?php
namespace Asyntai\Chatbot\Controller;

use Asyntai\Chatbot\Service\SettingsService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Http\JsonResponse;

class AjaxController
{
    public function saveAction(ServerRequestInterface $request): ResponseInterface
    {
        // Check backend user authentication
        $backendUser = $GLOBALS['BE_USER'] ?? null;
        if (!($backendUser instanceof BackendUserAuthentication) || !$backendUser->user) {
            return new JsonResponse(['success' => false, 'error' => 'Not authenticated'], 401);
        }

        $parsedBody = $request->getParsedBody() ?: [];
        if (empty($parsedBody)) {
            $content = (string)($request->getBody()->__toString());
            $json = json_decode($content, true);
            if (is_array($json)) {
                $parsedBody = $json;
            }
        }

        $siteId = isset($parsedBody['site_id']) ? trim((string)$parsedBody['site_id']) : '';
        if ($siteId === '') {
            return new JsonResponse(['success' => false, 'error' => 'missing site_id'], 400);
        }

        $payload = [
            'siteId' => $siteId,
        ];
        if (!empty($parsedBody['script_url'])) {
            $payload['scriptUrl'] = trim((string)$parsedBody['script_url']);
        }
        if (!empty($parsedBody['account_email'])) {
            $payload['accountEmail'] = trim((string)$parsedBody['account_email']);
        }

        SettingsService::saveSettings($payload);

        $saved = SettingsService::getSettings();
        return new JsonResponse([
            'success' => true,
            'saved' => [
                'site_id' => $saved['siteId'],
                'script_url' => $saved['scriptUrl'],
                'account_email' => $saved['accountEmail'],
            ],
        ]);
    }

    public function resetAction(ServerRequestInterface $request): ResponseInterface
    {
        // Check backend user authentication
        $backendUser = $GLOBALS['BE_USER'] ?? null;
        if (!($backendUser instanceof BackendUserAuthentication) || !$backendUser->user) {
            return new JsonResponse(['success' => false, 'error' => 'Not authenticated'], 401);
        }

        SettingsService::resetSettings();
        return new JsonResponse(['success' => true]);
    }
}



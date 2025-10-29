<?php
namespace Asyntai\Chatbot\Controller;

use Asyntai\Chatbot\Service\SettingsService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ModuleController
{
    public function __construct()
    {
    }

    public function indexAction(ServerRequestInterface $request): ResponseInterface
    {
        // Handle POST requests for save/reset
        if ($request->getMethod() === 'POST') {
            $parsedBody = $request->getParsedBody() ?: [];
            if (empty($parsedBody)) {
                $content = (string)($request->getBody()->__toString());
                $json = json_decode($content, true);
                if (is_array($json)) {
                    $parsedBody = $json;
                }
            }

            // Check if this is a save or reset action
            if (isset($parsedBody['site_id'])) {
                return $this->handleSave($parsedBody);
            } elseif (isset($parsedBody['action']) && $parsedBody['action'] === 'reset') {
                return $this->handleReset();
            }
        }

        // Normal GET request - show the settings page
        $settings = SettingsService::getSettings();

        // Generate nonce for CSP
        $nonce = base64_encode(random_bytes(16));
        
        ob_start();
        $siteId = htmlspecialchars((string)($settings['siteId'] ?? ''), ENT_QUOTES);
        $accountEmail = htmlspecialchars((string)($settings['accountEmail'] ?? ''), ENT_QUOTES);
        $scriptUrl = htmlspecialchars((string)($settings['scriptUrl'] ?? 'https://asyntai.com/static/js/chat-widget.js'), ENT_QUOTES);
        include GeneralUtility::getFileAbsFileName('EXT:asyntai_chatbot/Resources/Private/Templates/Backend/Settings.php');
        $content = (string)ob_get_clean();
        
        // Add JS inline with nonce
        $jsFile = GeneralUtility::getFileAbsFileName('EXT:asyntai_chatbot/Resources/Public/JavaScript/backend.js');
        $jsContent = file_exists($jsFile) ? file_get_contents($jsFile) : '';
        if ($jsContent) {
            $content .= '<script nonce="' . htmlspecialchars($nonce, ENT_QUOTES) . '">' . $jsContent . '</script>';
        }
        
        $response = new HtmlResponse($content);
        // Add CSP header with nonce
        return $response->withAddedHeader('Content-Security-Policy', "script-src 'self' 'nonce-" . $nonce . "' https://asyntai.com;");
    }

    protected function handleSave(array $parsedBody): ResponseInterface
    {
        $siteId = isset($parsedBody['site_id']) ? trim((string)$parsedBody['site_id']) : '';
        if ($siteId === '') {
            return new \TYPO3\CMS\Core\Http\JsonResponse(['success' => false, 'error' => 'missing site_id'], 400);
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
        return new \TYPO3\CMS\Core\Http\JsonResponse([
            'success' => true,
            'saved' => [
                'site_id' => $saved['siteId'],
                'script_url' => $saved['scriptUrl'],
                'account_email' => $saved['accountEmail'],
            ],
        ]);
    }

    protected function handleReset(): ResponseInterface
    {
        SettingsService::resetSettings();
        return new \TYPO3\CMS\Core\Http\JsonResponse(['success' => true]);
    }

    public function saveAction(ServerRequestInterface $request): ResponseInterface
    {
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
            return new \TYPO3\CMS\Core\Http\JsonResponse(['success' => false, 'error' => 'missing site_id'], 400);
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
        return new \TYPO3\CMS\Core\Http\JsonResponse([
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
        SettingsService::resetSettings();
        return new \TYPO3\CMS\Core\Http\JsonResponse(['success' => true]);
    }
}



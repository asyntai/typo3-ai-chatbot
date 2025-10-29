<?php
namespace Asyntai\Chatbot\Service;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SettingsService
{
    public const EXT_KEY = 'asyntai_chatbot';

    public static function getSettings(): array
    {
        /** @var ExtensionConfiguration $extConf */
        $extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        try {
            $config = $extConf->get(self::EXT_KEY) ?? [];
        } catch (\Throwable $e) {
            $config = [];
        }
        return [
            'siteId' => (string)($config['site_id'] ?? ''),
            'scriptUrl' => (string)($config['script_url'] ?? 'https://asyntai.com/static/js/chat-widget.js'),
            'accountEmail' => (string)($config['account_email'] ?? ''),
        ];
    }

    public static function saveSettings(array $data): void
    {
        $existing = self::getSettings();
        $merged = array_merge($existing, $data);
        $normalized = [
            'site_id' => (string)($merged['siteId'] ?? ''),
            'script_url' => (string)($merged['scriptUrl'] ?? 'https://asyntai.com/static/js/chat-widget.js'),
            'account_email' => (string)($merged['accountEmail'] ?? ''),
        ];
        /** @var ExtensionConfiguration $extConf */
        $extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $extConf->set(self::EXT_KEY, $normalized);
    }

    public static function resetSettings(): void
    {
        /** @var ExtensionConfiguration $extConf */
        $extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $extConf->set(self::EXT_KEY, [
            'site_id' => '',
            'script_url' => 'https://asyntai.com/static/js/chat-widget.js',
            'account_email' => '',
        ]);
    }
}



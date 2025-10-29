<?php
namespace Asyntai\Chatbot\Middleware;

use Asyntai\Chatbot\Service\SettingsService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\StreamFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class InjectWidgetMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        // Only modify HTML responses
        $contentType = $response->getHeaderLine('Content-Type');
        if (stripos($contentType, 'text/html') === false) {
            return $response;
        }

        $body = (string) $response->getBody();
        if ($body === '') {
            return $response;
        }

        $settings = SettingsService::getSettings();
        $siteId = trim((string)($settings['siteId'] ?? ''));
        if ($siteId === '') {
            return $response;
        }
        $scriptUrl = trim((string)($settings['scriptUrl'] ?? 'https://asyntai.com/static/js/chat-widget.js'));

        $scriptTag = '<script src="' . htmlspecialchars($scriptUrl, ENT_QUOTES) . '" async defer data-asyntai-id="' . htmlspecialchars($siteId, ENT_QUOTES) . '"></script>';

        // Insert before </body>
        $pos = strripos($body, '</body>');
        if ($pos !== false) {
            $body = substr($body, 0, $pos) . $scriptTag . substr($body, $pos);
        } else {
            $body .= $scriptTag;
        }

        /** @var StreamFactory $streamFactory */
        $streamFactory = GeneralUtility::makeInstance(StreamFactory::class);
        $newBody = $streamFactory->createStream($body);
        return $response->withBody($newBody);
    }
}



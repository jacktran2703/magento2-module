<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Api\Config\ChannelProviderInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Api\Config\ParametersSourceInterface;
use Omikron\Factfinder\Controller\Router;

class CommunicationConfig implements CommunicationConfigInterface, ParametersSourceInterface, ChannelProviderInterface
{
    private const PATH_CHANNEL               = 'factfinder/general/channel';
    private const PATH_ADDRESS               = 'factfinder/general/address';
    private const PATH_VERSION               = 'factfinder/advanced/version';
    private const PATH_DEFAULT_QUERY         = 'factfinder/advanced/default_query';
    private const PATH_IS_ENABLED            = 'factfinder/general/is_enabled';
    private const PATH_IS_ENRICHMENT_ENABLED = 'factfinder/general/ff_enrichment';
    private const PATH_DATA_TRANSFER_IMPORT  = 'factfinder/data_transfer/ff_push_import_enabled';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var UrlInterface */
    private $urlBuilder;

    public function __construct(ScopeConfigInterface $scopeConfig, UrlInterface $urlBuilder)
    {
        $this->scopeConfig = $scopeConfig;
        $this->urlBuilder  = $urlBuilder;
    }

    public function getChannel(int $scopeId = null): string
    {
        return (string) $this->scopeConfig->getValue(self::PATH_CHANNEL, ScopeInterface::SCOPE_STORES, $scopeId);
    }

    public function getAddress(): string
    {
        return (string) $this->scopeConfig->getValue(self::PATH_ADDRESS);
    }

    public function getDefaultQuery(): string
    {
        return (string) $this->scopeConfig->getValue(self::PATH_DEFAULT_QUERY);
    }

    public function isChannelEnabled(int $scopeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::PATH_IS_ENABLED, ScopeInterface::SCOPE_STORES, $scopeId);
    }

    public function isPushImportEnabled(int $scopeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::PATH_DATA_TRANSFER_IMPORT, ScopeInterface::SCOPE_STORES, $scopeId);
    }

    public function getParameters(): array
    {
        return [
            'url'           => $this->getServerUrl(),
            'version'       => $this->scopeConfig->getValue(self::PATH_VERSION),
            'default-query' => $this->getDefaultQuery(),
            'channel'       => $this->getChannel(),
        ];
    }

    private function getServerUrl(): string
    {
        if ($this->scopeConfig->isSetFlag(self::PATH_IS_ENRICHMENT_ENABLED, ScopeInterface::SCOPE_STORES)) {
            return $this->urlBuilder->getUrl('', ['_direct' => Router::FRONT_NAME]);
        }
        return $this->getAddress();
    }
}
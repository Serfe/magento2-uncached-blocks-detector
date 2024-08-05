<?php
/**
 * Copyright © Serfe S.A. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Serfe\UncacheableBlockDetector\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * @author Esteban Zeller <esteban@serfe.com>
 */
class Data extends AbstractHelper
{
    public const XML_PATH_ENABLED = 'dev/cache_detector/enabled';
    public const XML_PATH_DIE = 'dev/cache_detector/die';
    
    /** @var \Magento\Framework\App\State */
    private $appState;
    
    /** @var \Magento\PageCache\Model\Config */
    private $pageCacheConfig;
    
    /** @var boolean */
    private $isFullPageCacheEnabled;
    
    /** @var boolean */
    private $isVarnishEnabled;
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\PageCache\Model\Config $config
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\State $appState,
        \Magento\PageCache\Model\Config $config
    ) {
        parent::__construct($context);
        $this->appState = $appState;
        $this->pageCacheConfig = $config;
    }
        
    /**
     * Returns if the application is in developer mode
     *
     * @return bool
     */
    public function isInDeveloperMode() : bool
    {
        return $this->appState->getMode() === \Magento\Framework\App\State::MODE_DEVELOPER;
    }

    /**
     * Returns if the application is enabled
     *
     * @return bool
     */
    public function isEnabled() : bool
    {
        return (bool) $this->scopeConfig
              ->getValue(
                  self::XML_PATH_ENABLED,
                  \Magento\Store\Model\ScopeInterface::SCOPE_STORE
              );
    }
    
    /**
     * Is full page cache enabled
     *
     * @return bool
     */
    private function isFullPageCacheEnabled(): bool
    {
        if ($this->isFullPageCacheEnabled === null) {
            $this->isFullPageCacheEnabled = $this->pageCacheConfig->isEnabled();
        }
        return $this->isFullPageCacheEnabled;
    }

    /**
     * Is varnish cache engine enabled
     *
     * @return bool
     */
    private function isVarnishEnabled() : bool
    {
        if ($this->isVarnishEnabled === null) {
            $this->isVarnishEnabled = ($this->pageCacheConfig->getType() == \Magento\PageCache\Model\Config::VARNISH);
        }
        return $this->isVarnishEnabled;
    }
    
    /**
     * Return if feature is enabled
     *
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException If vanish is disabled
     * @throws \Magento\Framework\Exception\LocalizedException if full page cache is disabled
     */
    public function dieOnUncacheableBlock() : bool
    {
        if ($this->isEnabled() && $this->isInDeveloperMode()) {
            if (!$this->isVarnishEnabled()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Varnish is not enabled!')
                );
            }
            if (!$this->isFullPageCacheEnabled()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Full page cache is not enabled!')
                );
            }
            return (bool) $this->scopeConfig
                ->getValue(
                    self::XML_PATH_DIE,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
        }
        return false;
    }
}

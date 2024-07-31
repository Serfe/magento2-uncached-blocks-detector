<?php
/**
 * This file is part of Serfe/UncacheableBlocksDetector which is released under GNU General Public License
 * See COPYING.txt for license details.
 */
namespace Serfe\UncacheableBlockDetector\Rewrite\Magento\Framework\View;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Layout\Element;
use Psr\Log\LoggerInterface as Logger;

/**
 * @author Esteban Zeller <esteban@serfe.com>
 */
class Layout extends \Magento\Framework\View\Layout
{
  
  /**
   * @var \Serfe\UncacheableBlockDetector\Helper\Data
   */
  private $helper;
  
  /**
   * @param \Magento\Framework\View\Layout\ProcessorFactory $processorFactory
   * @param ManagerInterface $eventManager
   * @param \Magento\Framework\View\Layout\Data\Structure $structure
   * @param MessageManagerInterface $messageManager
   * @param \Magento\Framework\View\Design\Theme\ResolverInterface $themeResolver
   * @param \Magento\Framework\View\Layout\ReaderPool $readerPool
   * @param \Magento\Framework\View\Layout\GeneratorPool $generatorPool
   * @param \Magento\Framework\Cache\FrontendInterface $cache
   * @param \Magento\Framework\View\Layout\Reader\ContextFactory $readerContextFactory
   * @param \Magento\Framework\View\Layout\Generator\ContextFactory $generatorContextFactory
   * @param \Magento\Framework\App\State $appState
   * @param \Psr\Log\LoggerInterface $logger
   * @param bool $cacheable
   * @param SerializerInterface|null $serializer
   * @param int|null $cacheLifetime
   * @param \Serfe\UncacheableBlockDetector\Helper\Data $helper
   */
    public function __construct(
        \Magento\Framework\View\Layout\ProcessorFactory $processorFactory,
        ManagerInterface $eventManager,
        \Magento\Framework\View\Layout\Data\Structure $structure,
        MessageManagerInterface $messageManager,
        \Magento\Framework\View\Design\Theme\ResolverInterface $themeResolver,
        \Magento\Framework\View\Layout\ReaderPool $readerPool,
        \Magento\Framework\View\Layout\GeneratorPool $generatorPool,
        \Magento\Framework\Cache\FrontendInterface $cache,
        \Magento\Framework\View\Layout\Reader\ContextFactory $readerContextFactory,
        \Magento\Framework\View\Layout\Generator\ContextFactory $generatorContextFactory,
        AppState $appState,
        Logger $logger,
        $cacheable = true,
        SerializerInterface $serializer = null,
        ?int $cacheLifetime = null,
        \Serfe\UncacheableBlockDetector\Helper\Data $helper
    ) {
        parent::__construct(
            $processorFactory,
            $eventManager,
            $structure,
            $messageManager,
            $themeResolver,
            $readerPool,
            $generatorPool,
            $cache,
            $readerContextFactory,
            $generatorContextFactory,
            $appState,
            $logger,
            $cacheable,
            $serializer,
            $cacheLifetime
        );
        $this->helper = $helper;
    }
  /**
   * Check existed non-cacheable layout elements.
   *
   * @return bool
   */
    public function isCacheable()
    {
        $this->build();
        $elements = $this->getXml()->xpath('//' . Element::TYPE_BLOCK . '[@cacheable="false"]');
        $cacheable = $this->cacheable;
        foreach ($elements as $element) {
            $blockName = $element->getBlockName();
            if ($blockName !== false && $this->structure->hasElement($blockName)) {
                $cacheable = false;
                break;
            }
        }
      
        if (!$cacheable) {
            if ($this->helper->dieOnUncacheableBlock()) {
                $blocks = [];
                foreach ($elements as $element) {
                    $blockName = $element->getBlockName();
                    if ($blockName !== false && $this->structure->hasElement($blockName)) {
                        $blocks[] = $blockName;
                    }
                }
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Found uncacheable blocks: %1', implode(',', $blocks))
                );
            }
        }
      
        return $cacheable;
    }
}

<?php
/**
 * This file is part of Serfe/UncacheableBlocksDetector which is released under GNU General Public License
 * See COPYING.txt for license details.
 */

namespace Serfe\UncacheableBlockDetector\Test\Unit\Helper;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \Serfe\UncacheableBlockDetector\Helper\Data
 */
class DataTest extends TestCase
{
    /**
     * Mock context
     *
     * @var \Magento\Framework\App\Helper\Context|PHPUnit_Framework_MockObject_MockObject
     */
    private $context;

    /**
     * Mock appState
     *
     * @var \Magento\Framework\App\State|PHPUnit_Framework_MockObject_MockObject
     */
    private $appState;

    /**
     * Mock config
     *
     * @var \Magento\PageCache\Model\Config|PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Object to test
     *
     * @var \Serfe\UncacheableBlockDetector\Helper\Data
     */
    private $testObject;
    
    private $configMock;

    /**
     * Main set up method
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->objectManager = new ObjectManager($this);
        $this->configMock = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $this->context = $this->createMock(\Magento\Framework\App\Helper\Context::class);
        $this->context->expects($this->once())->method('getScopeConfig')->will($this->returnValue($this->configMock));
        $this->appState = $this->createMock(\Magento\Framework\App\State::class);
        $this->config = $this->createMock(\Magento\PageCache\Model\Config::class);
        $this->testObject = $this->objectManager->getObject(
            \Serfe\UncacheableBlockDetector\Helper\Data::class,
            [
                'context' => $this->context,
                'appState' => $this->appState,
                'config' => $this->config,
            ]
        );
    }

    /**
     * @return array
     */
    public function dataProviderForTestIsInDeveloperMode()
    {
        return [
            'Developer mode'  => [ \Magento\Framework\App\State::MODE_DEVELOPER , true  ], 
            'Production mode' => [ \Magento\Framework\App\State::MODE_PRODUCTION, false ], 
            'Default mode'    => [ \Magento\Framework\App\State::MODE_DEFAULT   , false ]
        ];
    }

    /**
     * @dataProvider dataProviderForTestIsInDeveloperMode
     */
    public function testIsInDeveloperMode( $state, $result)
    {
        $this->appState->method('getMode')->will($this->returnValue($state));
        $this->assertEquals($result, $this->testObject->isInDeveloperMode());
    }

    public function testIsEnabled()
    {
      $this->configMock->expects($this->once())
           ->method('getValue')
           ->with($this->equalTo(\Serfe\UncacheableBlockDetector\Helper\Data::XML_PATH_ENABLED))
           ->will($this->returnValue(true));
      $this->assertTrue($this->testObject->isEnabled());
    }
    
    public function testIsDisabled()
    {
      $this->configMock->expects($this->once())
           ->method('getValue')
           ->with($this->equalTo(\Serfe\UncacheableBlockDetector\Helper\Data::XML_PATH_ENABLED))
           ->will($this->returnValue(false));
      $this->assertFalse($this->testObject->isEnabled());
    }
}

<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Unit\Settings\General;

use ONGR\SettingsBundle\Settings\General\SettingAwareServiceFactory;
use ONGR\SettingsBundle\Exception\SettingNotFoundException;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;

class SettingAwareServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests get method.
     */
    public function testGetMethod()
    {
        $settingAwareServiceFactory = new SettingAwareServiceFactory($this->getSettingMock());

        $callMap = [
            'testMethod' => null,
            'setTestMethod' => 'setTestMethod',
        ];

        $this->assertEquals(
            $this->getTestObject(),
            $settingAwareServiceFactory->get($callMap, $this->getTestObject())
        );
    }

    /**
     * Tests get method logger.
     */
    public function testGetMethodLogger()
    {
        $settingAwareServiceFactory = $this->getSettingAwareServiceFactory();

        $logger = $this->getMock('Psr\Log\LoggerInterface');

        $logger->expects(
            $this->once()
        )->method('notice')
            ->with("Setting 'testMethod' was not found.");

        $settingAwareServiceFactory->setLogger($logger);

        $callMap = [
            'testMethod' => null,
        ];

        $settingAwareServiceFactory->get($callMap, $this->getTestObject());
    }

    /**
     * Tests get method logic exception.
     *
     * @expectedException LogicException
     */
    public function testGetMethodException()
    {
        $settingAwareServiceFactory = new SettingAwareServiceFactory($this->getSettingMock());
        $callMap = [
            'key' => null,
        ];

        $settingAwareServiceFactory->get($callMap, $this->getTestObject());
    }

    /**
     * Returns mock test Object.
     *
     * @return Setting
     */
    protected function getTestObject()
    {
        return $this->getMock('stdClass', ['setTestMethod']);
    }

    /**
     * Returns mock of Setting.
     *
     * @return Setting
     */
    protected function getSettingMock()
    {
        return $this->getMock('ONGR\SettingsBundle\Settings\General\SettingsContainerInterface');
    }

    /**
     * Returns mock of Setting aware service factory.
     *
     * @return Setting
     */
    protected function getSettingAwareServiceFactory()
    {
        $settingsContainer = $this->getSettingMock();

        $settingsContainer->expects(
            $this->at(0)
        )->method('get')
            ->will($this->throwException(new SettingNotFoundException));

        return new SettingAwareServiceFactory($settingsContainer);
    }
}

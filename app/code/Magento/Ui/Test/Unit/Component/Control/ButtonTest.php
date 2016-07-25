<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Ui\Test\Unit\Component\Control;

use \Magento\Ui\Component\Control\Button;

use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;

class ButtonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Button
     */
    protected $button;

    /**
     * @var Context| \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var UrlInterface| \PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilderMock;

    /**
     * Escaper
     *
     * @var Escaper| \PHPUnit_Framework_MockObject_MockObject
     */
    protected $escaperMock;

    protected function setUp()
    {
        $this->contextMock = $this->getMock(
            '\Magento\Framework\View\Element\Template\Context',
            ['getPageLayout', 'getUrlBuilder', 'getEscaper'],
            [],
            '',
            false
        );
        $this->urlBuilderMock = $this->getMockForAbstractClass('Magento\Framework\UrlInterface');
        $this->contextMock->expects($this->any())->method('getUrlBuilder')->willReturn($this->urlBuilderMock);
        $this->escaperMock = $this->getMock('Magento\Framework\Escaper', ['escapeHtml'], [], '', false);
        $this->contextMock->expects($this->any())->method('getEscaper')->willReturn($this->escaperMock);
        $this->button = new Button($this->contextMock);
    }

    public function testGetType()
    {
        $this->assertEquals('button', $this->button->getType());
    }

    public function testGetAttributesHtml()
    {
        $expected = 'type="button" class="action- scalable classValue disabled" '
            . 'onclick="location.href = \'url2\';" disabled="disabled" data-attributeKey="attributeValue" ';
        $this->button->setDisabled(true);
        $this->button->setData('url', 'url2');
        $this->button->setData('class', 'classValue');
        $this->button->setDataAttribute(['attributeKey' => 'attributeValue']);
        $this->escaperMock->expects($this->any())->method('escapeHtml')->withAnyParameters()->willReturnArgument(0);
        $this->assertEquals($expected, $this->button->getAttributesHtml());
    }

    /**
     * @param string|null $onClick
     * @param string|null $url
     * @param string $getUrl
     * @param string|null $result
     * @dataProvider dataProviderGetOnClick
     */
    public function testGetOnClick($onClick, $url, $getUrl, $result)
    {
        if ($onClick !== null) {
            $this->button->setData('on_click', $onClick);
        }
        if ($url !== null) {
            $this->button->setData('url', $url);
        }
        $this->urlBuilderMock->expects($this->any())
            ->method('getUrl')
            ->with('', [])
            ->willReturn($getUrl);

        $this->assertEquals($result, $this->button->getOnClick());
    }

    /**
     * @return array
     */
    public function dataProviderGetOnClick()
    {
        return [
            [null, null, '', null],
            [null, null, 'get_url', 'location.href = \'get_url\';'],
            ['on_click', null, null, 'on_click'],
            ['on_click', 'url', 'get_url', 'on_click'],
            ['on_click', null, '', 'on_click'],
            [null, 'url', 'get_url', 'location.href = \'url\';'],
            [null, 'url', '', 'location.href = \'url\';'],
        ];
    }
}
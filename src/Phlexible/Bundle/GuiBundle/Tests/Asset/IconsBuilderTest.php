<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset;

use org\bovigo\vfs\vfsStream;
use Phlexible\Bundle\GuiBundle\Asset\IconsBuilder;
use Phlexible\Component\GuiAsset\Asset\Asset;
use Phlexible\Component\GuiAsset\Compressor\CompressorInterface;
use Phlexible\Component\GuiAsset\Finder\ResourceFinderInterface;
use Puli\Repository\Resource\FileResource;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\IconsBuilder
 */
class IconsBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $imgFile = dirname(dirname(__DIR__)) . '/Resources/public/icons/component.png';

        $root = vfsStream::setup('phlexible');
        $icon = vfsStream::newFile('phlexiblegui/icons/icon.png')->at($root)->setContent(file_get_contents($imgFile));

        $finder = $this->prophesize(ResourceFinderInterface::class);
        $finder->findByType('phlexible/icons')->willReturn(array(
            new FileResource($icon->url(), '/'.$icon->path()),
        ));

        $compressor = $this->prophesize(CompressorInterface::class);

        $builder = new IconsBuilder($finder->reveal(), $compressor->reveal(), $root->url(), false);

        $result = $builder->build('/a');

        $expected = <<<EOF
/* Created: _date_ */
.p-gui-icon-icon {background-image: url(/a/bundles/phlexiblegui/icons/icon.png) !important;}

EOF;
;

        $this->assertFileExists($root->getChild('icons.css')->url());
        $this->assertEquals(new Asset($root->getChild('icons.css')->url()), $result);

        $result = file_get_contents($root->getChild('icons.css')->url());
        $result = preg_replace('/Created: (.+)\*/', 'Created: _date_ *', $result);
        $this->assertEquals($expected, $result);
    }
}
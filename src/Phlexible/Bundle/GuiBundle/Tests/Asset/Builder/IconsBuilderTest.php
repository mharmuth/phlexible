<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\Builder;

use org\bovigo\vfs\vfsStream;
use Phlexible\Bundle\GuiBundle\Asset\Asset;
use Phlexible\Bundle\GuiBundle\Asset\Builder\IconsBuilder;
use Phlexible\Bundle\GuiBundle\Asset\Finder\ResourceFinderInterface;
use Phlexible\Bundle\GuiBundle\Compressor\CompressorInterface;
use Puli\Repository\Resource\FileResource;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\Builder\IconsBuilder
 */
class IconsBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $imgFile = dirname(dirname(dirname(__DIR__))) . '/Resources/public/icons/component.png';

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

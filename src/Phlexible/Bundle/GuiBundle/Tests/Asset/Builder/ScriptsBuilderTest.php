<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\Builder;

use org\bovigo\vfs\vfsStream;
use Phlexible\Bundle\GuiBundle\Asset\Builder\ScriptsBuilder;
use Phlexible\Bundle\GuiBundle\Asset\Finder\ResourceFinderInterface;
use Phlexible\Bundle\GuiBundle\Asset\MappedAsset;
use Phlexible\Bundle\GuiBundle\Asset\MappedContent\MappedContent;
use Phlexible\Bundle\GuiBundle\Asset\MappedContent\MappedContentBuilder;
use Phlexible\Bundle\GuiBundle\Asset\ResourceResolver\ResolvedResources;
use Phlexible\Bundle\GuiBundle\Asset\ResourceResolver\ScriptsResourceResolver;
use Phlexible\Bundle\GuiBundle\Compressor\CompressorInterface;
use Prophecy\Argument;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\Builder\ScriptsBuilder
 */
class ScriptsBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $root = vfsStream::setup('phlexible');

        $finder = $this->prophesize(ResourceFinderInterface::class);
        $finder->findByType('phlexible/scripts')->willReturn(array());

        $resolver = $this->prophesize(ScriptsResourceResolver::class);
        $resolver->resolve(array())->willReturn(new ResolvedResources(array(), array()));

        $builder = $this->prophesize(MappedContentBuilder::class);
        $builder->build(Argument::cetera())->willReturn(new MappedContent('a', 'b'));

        $compressor = $this->prophesize(CompressorInterface::class);

        $builder = new ScriptsBuilder(
            $finder->reveal(),
            $resolver->reveal(),
            $builder->reveal(),
            $compressor->reveal(),
            $root->url(),
            false
        );

        $result = $builder->build();

        $this->assertFileExists($root->getChild('gui.js')->url());
        $this->assertFileExists($root->getChild('gui.js.map')->url());

        $expected = new MappedAsset($root->getChild('gui.js')->url(), $root->getChild('gui.js.map')->url());
        $this->assertEquals($expected, $result);
    }
}
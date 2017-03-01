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
use Phlexible\Bundle\GuiBundle\Asset\TranslationsBuilder;
use Phlexible\Component\Bundler\Asset\Asset;
use Phlexible\Component\Bundler\Compressor\CompressorInterface;
use Phlexible\Component\Bundler\Translation\TranslationBuilderInterface;
use Phlexible\Component\Bundler\Translation\TranslationExtractorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\TranslatorBagInterface;

/**
 * Translations builder test.
 *
 * @covers \Phlexible\Bundle\GuiBundle\Asset\TranslationsBuilder
 */
class TranslationsBuilderTest extends TestCase
{
    public function testBuild()
    {
        $root = vfsStream::setup();

        $messageCatalog = new MessageCatalogue('de', array());

        $translator = $this->prophesize(TranslatorBagInterface::class);
        $translator->getCatalogue('de')->willReturn($messageCatalog);

        $extractor = $this->prophesize(TranslationExtractorInterface::class);
        $extractor->extract($messageCatalog, 'testDomain')->willReturn(array());

        $builder = $this->prophesize(TranslationBuilderInterface::class);

        $compressor = $this->prophesize(CompressorInterface::class);
        $compressor->compressFile(vfsStream::path('/root/translations-de.js'));

        $builder = new TranslationsBuilder($translator->reveal(), $extractor->reveal(), $builder->reveal(), $compressor->reveal(), $root->url(), 'de', false);

        $result = $builder->build('de', 'testDomain');

        $this->assertEquals(new Asset('vfs://root/translations-de.js'), $result);
    }
}

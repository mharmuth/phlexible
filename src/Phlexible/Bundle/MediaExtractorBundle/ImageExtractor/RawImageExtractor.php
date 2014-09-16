<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle\ImageExtractor;

use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;

/**
 * Raw image extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RawImageExtractor implements ImageExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FileInterface $file)
    {
        return strtolower($file->getAssettype()) === 'image' || strtolower($file->getDocumenttype()) === 'pdf';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(FileInterface $file)
    {
        return $file->getPhysicalPath();
    }
}

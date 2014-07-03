<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\VideoExtractor;

use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * Raw video extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RawVideoExtractor implements VideoExtractorInterface
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
        return strtolower($file->getAttribute('assettype')) === 'video';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(FileInterface $file)
    {
        return $file->getPhysicalPath();
    }
}

<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendMediaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Frontend media bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleFrontendMediaBundle extends Bundle
{
    public function getFrontendPublishItems()
    {
        return $this->getContainer()->get('frontendmediamanagerFrontendPublishItems')->getItems();
    }
}

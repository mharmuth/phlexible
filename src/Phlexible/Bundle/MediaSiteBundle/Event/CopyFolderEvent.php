<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Event;

use Phlexible\Bundle\MediaSiteBundle\Driver\Action\CopyFolderAction;

/**
 * Copy folder event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @method CopyFolderAction getAction()
 */
class CopyFolderEvent extends AbstractActionFolderEvent
{
}
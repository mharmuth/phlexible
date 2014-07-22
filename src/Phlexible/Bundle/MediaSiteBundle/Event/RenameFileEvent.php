<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Event;

use Phlexible\Bundle\MediaSiteBundle\Driver\Action\RenameFileAction;

/**
 * Rename file event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @method RenameFileAction getAction()
 */
class RenameFileEvent extends AbstractActionFileEvent
{
}
<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Model;

use Phlexible\Bundle\MediaSiteBundle\Exception\RuntimeException;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteInterface;

/**
 * Folder iterator
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class FolderIterator implements \Iterator, \RecursiveIterator
{
    /**
     * @var \Iterator
     */
    private $iterator;

    /**
     * @param FolderInterface|SiteInterface $folder
     *
     * @throws RuntimeException
     */
    public function __construct($folder)
    {
        if (is_array($folder)) {
            $this->iterator = new \ArrayIterator($folder);
        } elseif ($folder instanceof FolderInterface) {
            $this->iterator = new \ArrayIterator([$folder]);
        } elseif ($folder instanceof SiteInterface) {
            $this->iterator = new \ArrayIterator([$folder->findRootFolder()]);
        } else {
            throw new RuntimeException('FolderIterator needs either Site or Folder.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->iterator->current();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->current()->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->iterator->next();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->iterator->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    /**
     * Get Iterator for currents element children.
     *
     * @return FolderIterator
     */
    public function getChildren()
    {
        return new FolderIterator($this->current()->getSite()->findFoldersByParentFolder($this->current()));
    }

    /**
     * Check if current element has children.
     *
     * @return bool
     */
    public function hasChildren()
    {
        return $this->current()->getSite()->countFoldersByParentFolder($this->current());
    }

}

<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TeaserBundle\Event;

use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TreeBundle\Entity\TreeNode;
use Symfony\Component\EventDispatcher\Event;

/**
 * Reorder teasers event.
 *
 * @author Peter Fahsel <pfahsel@brainbits.net>
 */
class ReorderTeasersEvent extends Event
{
    /**
     * @var TreeNode
     */
    private $node;

    /**
     * @var string
     */
    private $areaId;

    /**
     * @var Teaser[]
     */
    private $teasers;

    /**
     * @param TreeNode $node
     * @param int      $areaId
     * @param Teaser[] $teasers
     */
    public function __construct(TreeNode $node, $areaId, array $teasers)
    {
        $this->node = $node;
        $this->areaId = $areaId;
        $this->teasers = $teasers;
    }

    /**
     * @return TreeNode
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @return string
     */
    public function getAreaId()
    {
        return $this->areaId;
    }

    /**
     * @return Teaser[]
     */
    public function getTeasers()
    {
        return $this->teasers;
    }
}

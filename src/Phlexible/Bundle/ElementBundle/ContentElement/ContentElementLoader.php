<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ContentElement;

use Phlexible\Bundle\ElementBundle\ContentElement\Loader\LoaderInterface;
use Phlexible\Bundle\ElementBundle\ContentElement\Loader\XmlLoader;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Content element loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ContentElementLoader
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var XmlLoader
     */
    private $loader;

    /**
     * @var ContentElement[]
     */
    private $elements = [];

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param LoggerInterface          $logger
     * @param LoaderInterface          $loader
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger,
        LoaderInterface $loader)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
        $this->loader = $loader;
    }

    /**
     * @param int    $eid
     * @param int    $version
     * @param string $language
     *
     * @return ContentElement
     */
    public function load($eid, $version, $language)
    {
        $id = $eid.'_'.$language.'_'.$version;

        if (!isset($this->elements[$id])) {
            $this->elements[$id] = $this->loader->load($eid, $version, $language);
        }

        return $this->elements[$id];
    }

    /**
     * @return ContentElement[]
     */
    public function getElements()
    {
        return $this->elements;
    }
}

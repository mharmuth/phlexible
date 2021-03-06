<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TeaserBundle\Configurator;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Model\ElementSourceManagerInterface;
use Phlexible\Bundle\ElementRendererBundle\Configurator\Configuration;
use Phlexible\Bundle\ElementRendererBundle\Configurator\ConfiguratorInterface;
use Phlexible\Bundle\ElementRendererBundle\ElementRendererEvents;
use Phlexible\Bundle\ElementRendererBundle\Event\ConfigureEvent;
use Phlexible\Bundle\TeaserBundle\ContentTeaser\DelegatingContentTeaserManager;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Layout area configurator.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LayoutareaConfigurator implements ConfiguratorInterface
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var ElementSourceManagerInterface
     */
    private $elementSourceManager;

    /**
     * @var DelegatingContentTeaserManager
     */
    private $teaserManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ElementService                 $elementService
     * @param ElementSourceManagerInterface  $elementSourceManager
     * @param DelegatingContentTeaserManager $teaserManager
     * @param EventDispatcherInterface       $dispatcher
     * @param LoggerInterface                $logger
     */
    public function __construct(
        ElementService $elementService,
        ElementSourceManagerInterface $elementSourceManager,
        DelegatingContentTeaserManager $teaserManager,
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger)
    {
        $this->elementService = $elementService;
        $this->elementSourceManager = $elementSourceManager;
        $this->teaserManager = $teaserManager;
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Request $request, Configuration $renderConfiguration)
    {
        if (!$renderConfiguration->hasFeature('treeNode')) {
            return;
        }

        $elementtypeId = $renderConfiguration->get('contentElement')->getElementtypeId();
        $elementtype = $this->elementSourceManager->findElementtype($elementtypeId);

        //$layouts = [];
        $layoutareas = [];
        foreach ($this->elementSourceManager->findElementtypesByType('layout') as $layoutarea) {
            if (in_array($elementtype, $this->elementService->findAllowedParents($layoutarea))) {
                $layoutareas[] = $layoutarea;
            }
        }

        /* @var $treeNode TreeNodeInterface */
        $treeNode = $renderConfiguration->get('treeNode');
        $tree = $treeNode->getTree();
        $treeNodePath = $tree->getPath($treeNode);

        $language = $request->getLocale();
        $isPreview = $request->attributes->get('_preview', false);

        $this->teaserManager->setLanguage($language);

        $areas = [];

        foreach ($layoutareas as $layoutarea) {
            //$beforeAreaEvent = new Brainbits_Event_Notification(new stdClass(), 'before_area');
            //$this->_dispatcher->dispatch($beforeAreaEvent);

            //$templateFilename = '';
            //$templates = $layoutElementTypeVersion->getTemplates();

            //if (count($templates))
            //{
            //    $template = current($templates);
            //    $templateFilename = $template->getFilename();
            //}

            //$this->_debugTime('initTeasers - Layoutarea');
            //$this->_debugLine('Layoutarea: ' . $layoutElementTypeVersion->getTitle(), 'notice');

            $teasers = $this->teaserManager->findForLayoutAreaAndTreeNodePath($layoutarea, $treeNodePath, false);

            foreach ($teasers as $index => $teaser) {
                if (!$isPreview) {
                    $version = $this->teaserManager->getPublishedVersion($teaser, $language);
                    if (!$version) {
                        unset($teasers[$index]);
                        continue;
                    }
                } else {
                    $element = $this->elementService->findElement($teaser->getTypeId());
                    $elementVersion = $this->elementService->findLatestElementVersion($element);
                    $version = $elementVersion->getVersion();
                }

                $teaser->setVersion($version);
            }

            $areas[$layoutarea->getUniqueId()] = [
                'title' => $layoutarea->getTitle(),
                'uniqueId' => $layoutarea->getUniqueId(),
                'children' => $teasers,
            ];

            //$areaEvent = new Brainbits_Event_Notification(new stdClass(), 'area');
            //$this->_dispatcher->dispatch($areaEvent);
        }

        $renderConfiguration
            ->addFeature('layoutarea')
            ->setVariable('teasers', $areas);

        $event = new ConfigureEvent($renderConfiguration);
        $this->dispatcher->dispatch(ElementRendererEvents::CONFIGURE_LAYOUTAREA, $event);
    }
}

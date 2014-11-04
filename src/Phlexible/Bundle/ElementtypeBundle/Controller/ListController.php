<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Controller;

use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * List controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elementtypes/list")
 * @Security("is_granted('ROLE_ELEMENTTYPES')")
 */
class ListController extends Controller
{
    /**
     * List elementtypes
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="elementtypes_list")
     */
    public function listAction(Request $request)
    {
        $type = $request->get('type', Elementtype::TYPE_FULL);

        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');

        $elementtypes = array();
        foreach ($elementtypeService->findAllElementtypes() as $elementtype) {
            if ($type !== $elementtype->getType()) {
                continue;
            }

            if ($elementtype->getDeleted()) {
                continue;
            }

            $elementtypes[$elementtype->getTitle() . $elementtype->getId()] = array(
                'id'      => $elementtype->getId(),
                'title'   => $elementtype->getTitle(),
                'icon'    => $elementtype->getIcon(),
                'version' => $elementtype->getRevision(),
                'type'    => $elementtype->getType(),
            );
        }

        ksort($elementtypes);
        $elementtypes = array_values($elementtypes);

        return new JsonResponse(array(
            'elementtypes' => $elementtypes,
            'total'        => count($elementtypes)
        ));
    }

    /**
     * List elementtypes for type
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/fortype", name="elementtypes_list_fortype")
     */
    public function fortypeAction(Request $request)
    {
        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');

        $for = $request->get('type', Elementtype::TYPE_FULL);

        $elementtypes = $elementtypeService->findAllElementtypes();

        $allowedForFull = $allowedForStructure = $allowedForArea = array(
            Elementtype::TYPE_FULL,
            Elementtype::TYPE_STRUCTURE
        );
        $allowedForContainer = $allowedForPart = array(
            Elementtype::TYPE_LAYOUTAREA,
            Elementtype::TYPE_LAYOUTCONTAINER
        );

        $list = array();
        foreach ($elementtypes as $elementtype) {
            $type = $elementtype->getType();

            if ($for == Elementtype::TYPE_FULL && !in_array($type, $allowedForFull)) {
                continue;
            } elseif ($for == Elementtype::TYPE_STRUCTURE && !in_array($type, $allowedForStructure)) {
                continue;
            } elseif ($for == Elementtype::TYPE_REFERENCE) {
                continue;
            } elseif ($for == Elementtype::TYPE_LAYOUTAREA && !in_array($type, $allowedForArea)) {
                continue;
            } elseif ($for == Elementtype::TYPE_LAYOUTCONTAINER && !in_array($type, $allowedForContainer)) {
                continue;
            } elseif ($for == Elementtype::TYPE_PART && !in_array($type, $allowedForPart)) {
                continue;
            }

            $list[] = array(
                'id'      => $elementtype->getId(),
                'type'    => $elementtype->getType(),
                'title'   => $elementtype->getTitle(),
                'icon'    => $elementtype->getIcon(),
                'version' => $elementtype->getRevision()
            );

        }

        return new JsonResponse(array('elementtypes' => $list, 'total' => count($list)));
    }

    /**
     * Create an elementtype
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create", name="elementtypes_list_create")
     */
    public function createAction(Request $request)
    {
        $title = $request->get('title');
        $type = $request->get('type');
        $uniqueId = $request->get('unique_id');

        if (!$uniqueId) {
            $uniqueId = preg_replace('/[^a-z0-9_]/', '_', strtolower($title));
        }

        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $elementtypeService->createElementtype($type, $uniqueId, $title, null, null, array(), $this->getUser()->getId());

        return new ResultResponse(true, 'Element Type created.');
    }

    /**
     * Delete an elementtype
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/delete", name="elementtypes_list_delete")
     */
    public function deleteAction(Request $request)
    {
        $id = $request->get('id');

        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $elementtype = $elementtypeService->findElementtype($id);

        $elementtypeService->deleteElementtype($elementtype);

        return new ResultResponse(true, "Element type $id soft deleted.");
    }

    /**
     * Duplicate elementtype
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/duplicate", name="elementtypes_list_duplicate")
     */
    public function duplicateAction(Request $request)
    {
        $id = $request->get('id');

        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $sourceElementtype = $elementtypeService->findElementtype($id);

        $elementtype = $elementtypeService->duplicateElementtype($sourceElementtype, $this->getUser()->getUsername());

        return new ResultResponse(true, 'Element type duplicated.');
    }
}

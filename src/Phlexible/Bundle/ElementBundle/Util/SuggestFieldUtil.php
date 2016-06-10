<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Util;

use Phlexible\Bundle\DataSourceBundle\Entity\DataSourceValueBag;
use Phlexible\Component\MetaSet\Model\MetaDataManagerInterface;
use Phlexible\Component\MetaSet\Model\MetaSetManagerInterface;

/**
 * Utility class for suggest fields.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SuggestFieldUtil
{
    /**
     * @var MetaSetManagerInterface
     */
    private $metaSetManager;

    /**
    /**
     * @var MetaDataManagerInterface
     */
    private $metaDataManager;

    /**
     * @var string
     */
    private $separatorChar;

    /**
     * @param MetaSetManagerInterface  $metaSetManager
     * @param MetaDataManagerInterface $metaDataManager
     * @param string                   $separatorChar
     */
    public function __construct(MetaSetManagerInterface $metaSetManager, MetaDataManagerInterface $metaDataManager, $separatorChar)
    {
        $this->metaSetManager = $metaSetManager;
        $this->metaDataManager = $metaDataManager;
        $this->separatorChar = $separatorChar;
    }

    /**
     * Fetch all data source values used in any element versions.
     *
     * @param DataSourceValueBag $valueBag
     *
     * @return array
     */
    public function fetchUsedValues(DataSourceValueBag $valueBag)
    {
        $metaSets = $this->metaSetManager->findAll();

        $fields = array();
        foreach ($metaSets as $metaSet) {
            foreach ($metaSet->getFields() as $field) {
                if ($field->getOptions() === $valueBag->getDatasource()->getId()) {
                    $fields[] = $field;
                }
            }
        }

        $values = array();
        foreach ($fields as $field) {
            foreach ($this->metaDataManager->findByMetaSet($field->getMetaSet()) as $metaData) {
                $value = $metaData->get($field->getId(), $valueBag->getLanguage());

                $values[] = $value;
            }
        }
        
        // TODO: aus elementen

        $values = $this->splitSuggestValues($values);

        return $values;
    }

    /**
     * Split list of suggest values into pieces and remove duplicates.
     *
     * @param array $concatenated
     *
     * @return array
     */
    private function splitSuggestValues(array $concatenated)
    {
        $keys = [];
        foreach ($concatenated as $value) {
            $splitted = explode($this->separatorChar, $value);
            foreach ($splitted as $key) {
                $key = trim($key);

                // skip empty values
                if (strlen($key)) {
                    $keys[] = $key;
                }
            }
        }

        $uniqueKeys = array_unique($keys);

        return $uniqueKeys;
    }
}

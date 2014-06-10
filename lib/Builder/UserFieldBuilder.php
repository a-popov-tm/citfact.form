<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\Form\Builder;

use Bitrix\Main\Type;
use Bitrix\Main\Entity;
use Bitrix\Iblock;
use Bitrix\Highloadblock as HL;
use Citfact\Form\Exception\BuilderException;
use Citfact\Form\Type\ParameterDictionary;
use Citfact\Form\FormBuilderInterface;

class UserFieldBuilder implements FormBuilderInterface
{
    /**
     * @var array
     */
    protected $highLoadBlockFields;

    /**
     * @inheritdoc
     */
    public function create(ParameterDictionary $parameters)
    {
        $highLoadBlockId = (int)$parameters->get('HLBLOCK_ID');
        $highLoadBlock = HL\HighloadBlockTable::getById($highLoadBlockId)->fetch();
        if (empty($highLoadBlock)) {
            throw new BuilderException(sprintf('Not found highloadblock with id = %d', $highLoadBlockId));
        }

        $highLoadBlockFields = $this->getUserFieldManager()
            ->GetUserFields(sprintf('HLBLOCK_%d', $highLoadBlockId), 0, LANGUAGE_ID);

        $this->setHighLoadBlockFields($highLoadBlockFields);
        $this->setElementValue();
        $this->setSectionValue();
        $this->setEnumValue();

        return array(
            'DATA' => $highLoadBlock,
            'FIELDS' => $this->getHighLoadBlockFields(),
        );
    }

    /**
     * Set values for fields type enumeration
     *
     * @return void
     */
    protected function setEnumValue()
    {
        $enumList = $this->getListByType('enumeration');
        $fieldEnum = $this->getUserFieldEnum()->getList(array(), array('USER_FIELD_ID' => $enumList));
        while ($row = $fieldEnum->fetch()) {
            $row['SELECTED'] = 'N';
            foreach ($this->highLoadBlockFields as $fieldName => $field) {
                if ($field['ID'] == $row['USER_FIELD_ID']) {
                    $this->highLoadBlockFields[$fieldName]['VALUE'][] = $row;
                    break;
                }
            }
        }
    }

    /**
     * Set values for fields type iblock_element
     *
     * @return void
     */
    protected function setElementValue()
    {
        $iblockList = $this->getListByType('iblock_element');
        $queryBuilder = new Entity\Query(Iblock\ElementTable::getEntity());
        $queryBuilder
            ->setSelect(array('*'))
            ->setFilter(array('IBLOCK_ID' => $iblockList))
            ->setOrder(array());

        $elementResult = $queryBuilder->exec();
        while ($element = $elementResult->fetch()) {
            $element['SELECTED'] = 'N';
            foreach ($this->highLoadBlockFields as $fieldName => $field) {
                if ($field['SETTINGS']['IBLOCK_ID'] == $element['IBLOCK_ID']) {
                    $this->highLoadBlockFields[$fieldName]['VALUE'][] = $element;
                    break;
                }
            }
        }
    }

    /**
     * Set values for fields type iblock_section
     *
     * @return void
     */
    protected function setSectionValue()
    {
        $iblockList = $this->getListByType('iblock_section');
        $queryBuilder = new Entity\Query(Iblock\SectionTable::getEntity());
        $queryBuilder
            ->setSelect(array('*'))
            ->setFilter(array('IBLOCK_ID' => $iblockList))
            ->setOrder(array());

        $sectionResult = $queryBuilder->exec();
        while ($section = $sectionResult->fetch()) {
            $section['SELECTED'] = 'N';
            foreach ($this->highLoadBlockFields as $fieldName => $field) {
                if ($field['SETTINGS']['IBLOCK_ID'] == $section['IBLOCK_ID']) {
                    $this->highLoadBlockFields[$fieldName]['VALUE'][] = $section;
                    break;
                }
            }
        }
    }

    /**
     * @param string $type
     * @return array
     */
    protected function getListByType($type)
    {
        $list = array();
        foreach ($this->highLoadBlockFields as $field) {
            if ($field['USER_TYPE_ID'] != $type) {
                continue;
            }

            switch ($type) {
                case 'iblock_section':
                case 'iblock_element':
                    $list[] = $field['SETTINGS']['IBLOCK_ID'];
                    break;

                case 'enumeration':
                    $list[] = $field['ID'];
            }
        }

        return $list;
    }

    /**
     * Set highload block fields
     *
     * @param array $fields
     */
    public function setHighLoadBlockFields($fields)
    {
        $this->highLoadBlockFields = $fields;
    }


    /**
     * Return highload block fields
     *
     * @return array
     */
    public function getHighLoadBlockFields()
    {
        return $this->highLoadBlockFields;
    }

    /**
     * @return \CUserTypeManager
     */
    protected function getUserFieldManager()
    {
        return new \CUserTypeManager();
    }

    /**
     * @return \CUserFieldEnum
     */
    protected function getUserFieldEnum()
    {
        return new \CUserFieldEnum();
    }
}
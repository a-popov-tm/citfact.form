<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Display custom fields
 *
 * @param array $arResult
 * @return mixed
 */
$userTypePrint = function ($arResult) {
    $fieldList = $arResult['VIEW'];
    ?>
    <? foreach ($fieldList as $feildName => $fieldValue): ?>
        <?switch ($fieldValue['TYPE']):

            case 'input': ?>
                <div class="form-group">
                    <label><?= $fieldValue['LABEL'] ?></label>
                    <input type="text" class="form-control" name="<?= $fieldValue['NAME'] ?>"
                           value="<?= $fieldValue['VALUE'] ?>"/>
                </div>
                <? break; ?>

            <? case 'date': ?>
                <div class="form-group">
                    <label><?= $fieldValue['LABEL'] ?></label>
                    <div class="calendar-container">
                        <input type="text" class="form-control" name="<?= $fieldValue['NAME'] ?>"
                               value="<?= $fieldValue['VALUE'] ?>"/>
                        <span class="calendar" title="<?= GetMessage('CHOOSE_DATE') ?>"
                              onclick="BX.calendar({ node: this, field: '<?= $fieldValue['NAME'] ?>', bTime: true, bHideTime: false });"></span>
                    </div>
                </div>
                <? break; ?>

            <? case 'textarea': ?>
                <div class="form-group">
                    <label><?= $fieldValue['LABEL'] ?></label>
                    <textarea class="form-control"
                              name="<?= $fieldValue['NAME'] ?>"><?= $fieldValue['VALUE'] ?></textarea>
                </div>
                <? break; ?>

            <? case 'select': ?>
                <div class="form-group">
                    <label><?= $fieldValue['LABEL'] ?></label>
                    <? $multiple = ($fieldValue['MULTIPLE'] == 'Y') ? 'multiple="multiple"' : ''  ?>
                    <select class="form-control" name="<?= $fieldValue['NAME'] ?>" <?= $multiple ?>>
                        <? foreach ($fieldValue['VALUE_LIST'] as $value): ?>
                            <? $selected = ($fieldValue['MULTIPLE'] == 'Y')
                                ? (in_array($value['ID'], $fieldValue['VALUE'])) ? 'selected="selected"' : ''
                                : ($value['ID'] == $fieldValue['VALUE']) ? 'selected="selected"' : '';
                            ?>
                            <option value="<?= $value['ID'] ?>" <?= $selected ?>><?= $value['VALUE'] ?></option>
                        <? endforeach; ?>
                    </select>
                </div>
                <? break; ?>

            <? case 'checkbox': ?>
                <div class="form-group">
                    <label><?= $fieldValue['LABEL'] ?></label>
                    <? foreach ($fieldValue['VALUE_LIST'] as $value): ?>
                        <? $checked = ($fieldValue['MULTIPLE'] == 'Y')
                            ? (in_array($value['ID'], $fieldValue['VALUE'])) ? 'checked="checked"' : ''
                            : ($value['ID'] == $fieldValue['VALUE']) ? 'checked="checked"' : '';
                        ?>
                        <input type="checkbox" name="<?= $fieldValue['NAME'] ?>"
                               value="<?= $value['ID'] ?>" <?= $checked ?>/> <?= $value['VALUE'] ?>
                    <? endforeach; ?>
                </div>
                <? break; ?>

            <? case 'radio': ?>
                <div class="form-group">
                    <label><?= $fieldValue['LABEL'] ?></label>
                    <? foreach ($fieldValue['VALUE_LIST'] as $value): ?>
                        <? $checked = ($value['ID'] == $fieldValue['VALUE']) ? 'checked="checked"' : ''; ?>
                        <input type="radio" name="<?= $fieldValue['NAME'] ?>"
                               value="<?= $value['ID'] ?>" <?= $checked ?>/> <?= $value['VALUE'] ?>
                    <? endforeach; ?>
                </div>
                <? break; ?>

            <? case 'file': ?>
                <div class="form-group">
                    <label><?= $fieldValue['LABEL'] ?></label>
                    <input type="file" name="<?= $fieldValue['NAME'] ?>"/>
                </div>
                <? break; ?>

         <? endswitch; ?>
    <? endforeach; ?>
<? }; ?>

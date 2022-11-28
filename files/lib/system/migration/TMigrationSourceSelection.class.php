<?php

namespace wcf\system\migration;

use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;

trait TMigrationSourceSelection
{
    protected function createFormSourceSelection(): void
    {
        $availableSourceSystems = [
            0 => 'wcf.global.noSelection',
            1 => 'WoltLab Suite 5.5',
            2 => 'WoltLab Suite 6.0',
            11 => 'XenForo 2.0',
            12 => 'XenForo 2.1',
        ];

        $this->getForm()->appendChildren([
            FormContainer::create('sourceSelection')
                ->label('wcf.acp.dataMigration.sourceSelection')
                ->appendChildren([
                    SingleSelectionFormField::create('source')
                        ->label('wcf.acp.dataMigration.source')
                        ->options($availableSourceSystems)
                        ->addValidator(new FormFieldValidator(
                            'emptySelection',
                            function (SingleSelectionFormField $formField) {
                                if (empty($formField->getValue()) && empty($formField->getValidationErrors())) {
                                    $formField->addValidationError(new FormFieldValidationError('empty'));
                                }
                            }
                        ))
                        ->required(),
                ]),
        ]);
    }
}

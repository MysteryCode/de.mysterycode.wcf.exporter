<?php

namespace wcf\system\migration;

use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\LanguageItemFormNode;

trait TMigrationStepStart
{
    protected function createFormStart(): void
    {
        $this->getForm()->appendChildren([
            FormContainer::create('intro')
                ->label('wcf.acp.dataMigration')
                ->appendChildren([
                    LanguageItemFormNode::create('introText')
                        ->languageItem('wcf.acp.dataMigration.intro')
                ]),
        ]);
    }
}

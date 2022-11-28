<?php

namespace wcf\system\migration;

use wcf\data\package\PackageCache;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\CheckboxFormField;

trait TMigrationDataSelection
{
    protected function createFormDataSelection(): void
    {
        $availablePackages = [];
        foreach (PackageCache::getInstance()->getPackages() as $package) {
            $availablePackages[$package->getObjectID()] = $package->getTitle();
        }

        foreach ($availablePackages as $packageID => $package) {
            $this->getForm()->appendChild(
                FormContainer::create('data' . $packageID)
                    ->label($package)
                    ->appendChildren([
                        CheckboxFormField::create('data' . $packageID . '-' . '1')
                            ->label('data 1'),
                    ])
            );
        }
    }
}

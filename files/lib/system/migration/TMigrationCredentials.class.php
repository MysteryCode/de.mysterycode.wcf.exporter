<?php

namespace wcf\system\migration;

use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\IntegerFormField;
use wcf\system\form\builder\field\PasswordFormField;
use wcf\system\form\builder\field\TextFormField;

trait TMigrationCredentials
{
    protected function createFormCredentials(): void
    {
        $this->getForm()->appendChildren([
            FormContainer::create('credentials')
                ->label('wcf.acp.dataMigration.credentials')
                ->appendChildren([
                    TextFormField::create('dbHost')
                        ->label('wcf.acp.dataMigration.credentials.dbHost')
                        ->required(),
                    TextFormField::create('dbName')
                        ->label('wcf.acp.dataMigration.credentials.dbName')
                        ->required(),
                    TextFormField::create('dbUser')
                        ->label('wcf.acp.dataMigration.credentials.dbUser')
                        ->required(),
                    PasswordFormField::create('dbPassword')
                        ->label('wcf.acp.dataMigration.credentials.dbPassword')
                        ->required(),
                    IntegerFormField::create('dbPrefix')
                        ->label('wcf.acp.dataMigration.credentials.dbPrefix')
                        ->minimum(1)
                        ->value(1)
                        ->required(),
                ]),
        ]);
    }
}

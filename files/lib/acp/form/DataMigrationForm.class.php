<?php

namespace wcf\acp\form;

use wcf\form\AbstractForm;
use wcf\system\event\EventHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\form\builder\FormDocument;
use wcf\system\form\builder\IFormDocument;
use wcf\system\migration\TMigrationCredentials;
use wcf\system\migration\TMigrationDataSelection;
use wcf\system\migration\TMigrationSourceSelection;
use wcf\system\migration\TMigrationStepStart;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;

class DataMigrationForm extends AbstractForm
{
    use TMigrationStepStart;
    use TMigrationSourceSelection;
    use TMigrationCredentials;
    use TMigrationDataSelection;

    /**
     * @inheritDoc
     */
    public $activeTabMenuItem = 'wcf.acp.menu.link.maintenance.migration';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.management.canImportData'];

    /**
     * name of the form document class
     */
    protected string $formClassName = FormDocument::class;

    /**
     * form document
     */
    protected IFormDocument $form;

    protected string $step = 'start';

    /**
     * @var string[]
     */
    protected array $validSteps = [
        0 => 'start',               // welcome and information
        10 => 'sourceSelection',    // show a list of supported source-systems
        20 => 'credentials',        // store source system selection and ask for credentials
        30 => 'dataSelection',      // store credentials and ask what to migrate
        40 => 'settings',           // store data for migration and set some additional settings if needed
        50 => 'migrate',            // store additional settings and run migration
        60 => 'result',             // runs after migration
    ];

    protected function getNextStep(): ?string
    {
        $steps = $this->validSteps;
        \ksort($steps);
        $next = false;

        foreach ($steps as $step) {
            if ($next) {
                return $step;
            } elseif ($step === $this->step) {
                $next = true;
            }
        }

        return null;
    }

    public function getForm(): IFormDocument
    {
        return $this->form;
    }

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (!empty($_GET['step'])) {
            if (!\is_string($_GET['step']) || !\in_array($_GET['step'], $this->validSteps)) {
                throw new IllegalLinkException();
            }

            $this->step = StringUtil::trim($_GET['step']);
        } else {
            $this->step = 'start';
        }
    }

    /**
     * @inheritDoc
     */
    public function checkPermissions()
    {
        parent::checkPermissions();

        $this->buildForm();
    }

    /**
     * Builds the submitted form.
     */
    public function buildForm()
    {
        $this->createForm();

        EventHandler::getInstance()->fireAction($this, 'createForm');

        $this->form->build();

        EventHandler::getInstance()->fireAction($this, 'buildForm');

        $methodName = 'buildForm' . \ucfirst($this->step);
        if (\method_exists($this, $methodName)) {
            $this->{$methodName}();
        }
    }

    /**
     * Creates the form object.
     */
    protected function createForm()
    {
        $this->form = $this->formClassName::create('dataImport' . \ucfirst($this->step));

        $methodName = 'createForm' . \ucfirst($this->step);
        if (\method_exists($this, $methodName)) {
            $this->{$methodName}();
        }
    }

    /**
     * @inheritDoc
     */
    public function readData()
    {
        parent::readData();

        $this->setFormAction();
    }

    /**
     * @inheritDoc
     */
    public function readFormParameters()
    {
        parent::readFormParameters();

        $this->form->readValues();
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        parent::save();

        $formData = $this->form->getData();
        if (!isset($formData['data'])) {
            $formData['data'] = [];
        }

        $methodName = 'save' . \ucfirst($this->step);
        if (\method_exists($this, $methodName)) {
            $this->{$methodName}();
        }

        $this->saved();
    }

    /**
     * @inheritDoc
     */
    public function saved()
    {
        parent::saved();

        $this->form->showSuccessMessage();
        WCF::getTPL()->assign('success', true);

        $nextStep = $this->getNextStep();
        $parameters = [];
        if ($nextStep !== 'start') {
            $parameters['step'] = $nextStep;
        }

        HeaderUtil::redirect(LinkHandler::getInstance()->getControllerLink(self::class, $parameters));
    }

    /**
     * Sets the action of the form.
     */
    protected function setFormAction()
    {
        $parameters = [
            'step' => $this->step,
        ];

        $this->form->action(LinkHandler::getInstance()->getControllerLink(static::class, $parameters));
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
        parent::validate();

        $this->form->validate();

        if ($this->form->hasValidationErrors()) {
            throw new UserInputException($this->form->getPrefixedId());
        }
    }

    /**
     * @inheritDoc
     */
    protected function validateSecurityToken()
    {
        // does nothing, is handled by `IFormDocument` object
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'form' => $this->form,
            'step' => $this->step,
            'nextStep' => $this->getNextStep(),
        ]);
    }
}

<?php

/**
 * Base controller for the application.
 * Add general things in this controller.
 */
class ApplicationController extends Controller
{
    protected Model $_model;
    protected array $_validatedParm;

    public function setWriterModel(): void
    {
        $this->_model = new (Models::WRITER->value);
    }

    public function setReaderModel(): void
    {
        $this->_model = new (Models::READER->value);
    }

    private function validateParms(): void
    {

        $initialParms = parent::_getAllParams();
        $validations = [
            'id' => [
                'filter' => FILTER_VALIDATE_REGEXP,
                'options' => [
                    'regexp' => '/^[a-z0-9]+$/'
                ]
            ],
            'status' => FILTER_UNSAFE_RAW,
            'from' => [
                'filter' => FILTER_VALIDATE_REGEXP,
                'options' => [
                    'regexp' => '/^([01][0-9]|2[0-3]):[0-5][0-9]$/'
                ]
            ],
            'to' =>  [
                'filter' => FILTER_VALIDATE_REGEXP,
                'options' => [
                    'regexp' => '/^([01][0-9]|2[0-3]):[0-5][0-9]$/'
                ]
            ],
            'keywords' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'createdBy' => [
                'filter' => FILTER_VALIDATE_REGEXP,
                'options' => [
                    'regexp' => '/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/'
                ]
            ]
        ];
        $validatedParms = filter_var_array($initialParms, $validations);

        foreach ($initialParms as $parm => $value) {
            if ($validatedParms[$parm] === false) {
                $comment = match ($parm) {
                    'id' => 'an non alphanumeric identification withou special characters have been generated.',
                    'status' => 'select a pre-defined value of the filer \"stauts\"',
                    'from', 'to' => 'incorrect time format.',
                    'keywords' => 'keywords have not been sanitized correctly, check for special characters.',
                    'createdBy' => 'user name should be alphanumerical only.',
                    default => 'failed input data validation, check the system logs.'
                };
                throw new InvalidArgumentException("Invalid $parm format, $comment");
            }
        }
    }

    #[Override]
    public function beforeFilters()
    {
        try {
            $this->validateParms();
        } catch (InvalidArgumentException $e) {
            $this->view->disableView();
            $this->view->disableLayout();
            $errorController = new ErrorController;
            $errorController->setException($e);
            $errorController->execute('error');
            exit;
        }
    }
}

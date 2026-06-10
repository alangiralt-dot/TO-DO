<?php

/**
 * Base controller for the application.
 * Add general things in this controller.
 */
class ApplicationController extends Controller
{
    protected Model $_model;

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
        if (count($initialParms) > 0) {
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
                    throw new InvalidArgumentException("Invalid $parm format: $value, $comment");
                }
            }
            $this->_namedParameters = $validatedParms;
        }
    }
    //Overrriding beforFilter to apply validation if any parameter is passed.
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
        if (isset($this->_validatedParms['keywords'])) {
            $this->_namedParameters['keywords'] = explode(' ', strtolower($this->_namedParameters['keywords']));
        }
    }

    // Function to get the validation status of each parameter after beforeFilters is run
    public function getParmId(): ?string
    {
        return $this->_namedParameters['id'] ?? null;
    }
    public function getParmStatus(): ?Status
    {
        return isset($this->_namedParameters['status']) ? Status::from($this->_namedParameters['status']) : null;
    }
    public function getParmTo(): ?DateTime
    {
        $time = DateTime::createFromFormat('H:i', $this->_namedParameters['to'], new DateTimeZone(date_default_timezone_get()));
        return $time ?: null;
    }
    public function getParmFrom(): ?DateTime
    {
        $time = DateTime::createFromFormat('H:i', $this->_namedParameters['from'], new DateTimeZone(date_default_timezone_get()));
        return $time ?: null;
    }
    public function getParmKeywords(): ?array
    {
        return $this->_namedParameters['keywords'] ?? null;
    }
    public function getParmCreatedBy(): ?string
    {
        return $this->_namedParameters['createdBy'] ?? null;
    }
    public function getTasksArray(): array
    {
        $tasks_as_array = [];
        foreach ($this->view->tasks as $task) {
            $tasks_as_array[] = $task->getArray();
        }
        return $tasks_as_array;
    }
    public function setTasks(array $tasks_in_model)
    {
        $task_as_object = [];
        foreach ($tasks_in_model as $task_array) {
            try {
                $task_as_object[] = new Task(
                    $task_array['id'], // property id
                    $task_array['description'], // property description
                    $task_array['status'], // property status
                    $task_array['start_time'], // property from
                    $task_array['end_time'], // property to
                    $task_array['created_by'] // property createdBy
                );
            } catch (ValueError | TypeError | InvalidArgumentException $e) {
                $this->view->disableView();
                $this->view->disableLayout();
                $errorController = new ErrorController;
                $errorController->setException($e);
                $errorController->execute('error');
                exit;
            }
        }
        $this->view->tasks = $task_as_object;
    }
}

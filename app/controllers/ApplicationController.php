<?php

/**
 * Base controller for the application.
 * Add general things in this controller.
 */
class ApplicationController extends Controller
{
    protected Model $_model;

    protected function handleError(\Throwable $e): void
    {
        $this->view->disableView();
        $this->view->disableLayout();
        $errorController = new ErrorController();
        $errorController->setException($e);
        $errorController->execute('error');
        exit;
    }

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

        $initialParms = array_filter(parent::_getAllParams()); //EDB: Fix para eliminar los parametros que no se pasaron.
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
            $validatedParms = array_filter(filter_var_array($initialParms, $validations));

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
            $this->handleError($e);
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
    public function setTasks(?array $tasks_in_model = null)
    {
        if (!is_null($tasks_in_model) && !empty($tasks_in_model)) {
            $task_as_object = [];
            foreach ($tasks_in_model as $task) {
                if (is_array($task)) {
                    try {
                        $task_as_object[] = new Task(
                            $task['id'], // property id
                            $task['description'], // property description
                            $task['status'], // property status
                            $task['start_time'], // property from
                            $task['end_time'], // property to
                            $task['created_by'] // property createdBy
                        );
                    } catch (ValueError | TypeError | InvalidArgumentException $e) {
                        $this->handleError($e);
                    }
                } else if ($task instanceof stdClass) {
                    try {
                        $task_as_object[] = new Task(
                            $task->id, // property id
                            $task->description, // property description
                            $task->status, // property status
                            $task->start_time, // property from
                            $task->end_time, // property to
                            $task->created_by // property createdBy
                        );
                    } catch (ValueError | TypeError | InvalidArgumentException $e) {
                        $this->handleError($e);
                    }
                } else if ($task instanceof Task) {
                    $task_as_object[] = $task;
                } else {
                    $e = new InvalidArgumentException("Task data type is not array, stdClass or Task to be stored.");
                    $this->handleError($e);
                }
            }
            $this->view->tasks = $task_as_object;
        }
    }
}

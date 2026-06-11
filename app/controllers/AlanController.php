<?php
class AlanController extends ApplicationController {
    private ManageTasksToController $writer;
    private ReadTasksToController $reader;
    public function __construct() {
        $this->writer = new DataWriter();
        $this->reader = new DataReader();
    }
    public function openAction() {
        $welcomeHeaders = [
            "Welcome to your Workspace!",
            "Great to see you back!",
            "Let's achieve your goals today!",
            "Ready to get things done?",
            "Organization is the key to success!"
        ];
        if ($this->_getParam('cancelled') === '1') {
            $this->view->responseHeaderMessage = $this->getRandomCancelHeader();
        } else {
            $this->view->responseHeaderMessage = $welcomeHeaders[array_rand($welcomeHeaders)];
        }
        
        $this->view->responseBodyMessage = $this->getRandomWelcomeMessage();
        
        $this->view->breakConventionToReuseViews('partials/_response.phtml');
    }
    public function createAction() {
        $this->view->breakConventionToReuseViews('partials/_form.phtml');
    }
    public function addAction() {
        if ($this->getRequest()->isPost()) {
            $formData = [
                'description' => $this->_getParam('description'),
                'status'      => $this->_getParam('status'),
                'created_by'  => $this->_getParam('created_by'),
                'start_time'  => $this->_getParam('start_time') ?? '',
                'end_time'    => $this->_getParam('end_time') ?? ''
            ];

            $this->writer->addTask($formData);

            $this->view->responseHeaderMessage = "Task Saved Successfully!";
            $this->view->responseBodyMessage   = $this->getRandomWelcomeMessage();

            $this->view->breakConventionToReuseViews('partials/_response.phtml');
        }
    }
    public function updateAction() {
        if ($this->getRequest()->isPost()) {
            $formData = [
                'id'          => $this->_getParam('id'),
                'description' => $this->_getParam('description'),
                'status'      => $this->_getParam('status'),
                'created_by'  => $this->_getParam('created_by'),
                'start_time'  => $this->_getParam('start_time') ?? '',
                'end_time'    => $this->_getParam('end_time') ?? ''
            ];

            $this->writer->updateTask($formData);

            $this->view->responseHeaderMessage = "Task Updated Successfully!";
            $this->view->responseBodyMessage   = $this->getRandomWelcomeMessage();

            $this->view->breakConventionToReuseViews('partials/_response.phtml');
        }
    }

    public function editAction() {
        $taskId = $this->_getParam('id');
        $realTask = $this->reader->getTaskById($taskId);
        if ($realTask !== null) {
            $this->view->taskData = $realTask;
        }
        $this->view->breakConventionToReuseViews('partials/_form.phtml');
    }
    private function getRandomWelcomeMessage(): string {
        $welcomeMessages = [
            "Click 'List Tasks' at the top to explore your team's board, or click 'Add Task' to initiate a brand new project.",
            "Check your current workload using 'List Tasks' or create a new objective clicking 'Add Task'.",
            "Use the navigation menu at the top to 'List Tasks' or prepare a fresh action with 'Add Task'."
        ];
        return $welcomeMessages[array_rand($welcomeMessages)];
    }
    private function getRandomCancelHeader(): string {
        $cancelHeaders = [
            "Second thoughts are best!",
            "Changed your mind? Wise choice.",
            "Aborting mission! No tasks were harmed.",
            "Breathe in, breathe out. Plans change!",
            "Clean slate! No pressure."
        ];
        return $cancelHeaders[array_rand($cancelHeaders)];
    }
}
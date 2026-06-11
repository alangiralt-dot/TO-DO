<?php
class AlanController extends ApplicationController {
    public function openAction() {
        $welcomeHeaders = [
            "Welcome to your Workspace!",
            "Great to see you back!",
            "Let's achieve your goals today!",
            "Ready to get things done?",
            "Organization is the key to success!"
        ];

        $this->view->responseHeaderMessage = $welcomeHeaders[array_rand($welcomeHeaders)];
        $this->view->responseBodyMessage = $this->getRandomWelcomeMessage();
        
        $this->view->breakConventionToReuseViews('partials/_response.phtml');
    }
    public function createAction() {
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
}
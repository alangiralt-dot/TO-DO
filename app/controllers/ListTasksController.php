<?php
class ListTasksController extends ApplicationController
{

    public function listAction()
    {
        //Set the model to Reader.
        $this->setReaderModel();

        //Run request through the Model.
        try {
            $tasks = $this->_model->fetchByParms($this->_namedParameters);
        } catch (RuntimeException $e) {
            $this->handleError($e);
        }

        //Store tasks as objects
        $this->setTasks($tasks);
    }
}

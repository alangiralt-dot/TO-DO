<?php
// app/controllers/ManageTasksToController.php

interface ManageTasksToController {
    public function addTask(array $data): bool;
    //public function updateTask(string $id, array $data): bool;
    //public function deleteTask(string $id): bool;
}

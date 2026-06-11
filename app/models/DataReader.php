<?php
// app/models/DataReader.php

class DataReader implements ReadTasksToController {
    private string $filePath = ROOT_PATH . '/app/models/seed_to_do.json';

    public function getTaskById(string $id): ?array {
        if (!file_exists($this->filePath)) {
            return null;
        }

        $tasks = json_decode(file_get_contents($this->filePath), true) ?? [];

        foreach ($tasks as $task) {
            if ($task['id'] === $id) {
                return $task;
            }
        }

        return null;
    }
}

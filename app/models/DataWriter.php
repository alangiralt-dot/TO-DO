<?php
// app/models/DataWriter.php

class DataWriter implements ManageTasksToController {
    private string $filePath;

    public function __construct() {
        $this->filePath = ROOT_PATH . '/app/models/seed_to_do.json';
    }

    private function readTasks(): array {
        if (!file_exists($this->filePath)) {
            return [];
        }
        return json_decode(file_get_contents($this->filePath), true) ?? [];
    }

    private function saveTasks(array $tasks): bool {
        // This function returns the number of bytes that were written to the file, or false on failure.
        return file_put_contents($this->filePath, json_encode($tasks, JSON_PRETTY_PRINT)) !== false;
    }

    public function addTask(array $data): bool {
        $tasks = $this->readTasks();
        $tasks[] = [
            "id"          => bin2hex(random_bytes(16)),
            "description" => $data['description'],
            "status"      => $data['status'],
            "start_time"  => $data['start_time'],
            "end_time"    => $data['end_time'],
            "created_by"  => $data['created_by']
        ];
        return $this->saveTasks($tasks);
    }
}

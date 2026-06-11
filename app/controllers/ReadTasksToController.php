<?php
// app/controllers/ReadTasksToController.php

interface ReadTasksToController {
    public function getTaskById(string $id): ?array;
}

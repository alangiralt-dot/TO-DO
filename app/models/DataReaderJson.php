<?php

class DataReaderJson extends Model implements Reader
{

    public const PERSISTANCE_PATH = ROOT_PATH . '/app/models/persistance/persistanceData.json';
    #[Override]
    public function __construct()
    {
        $this->init();
    }

    private function matchByParms(array|stdClass $task, array $parms): bool
    {

        $condition = true;
        foreach ($parms as $parmName => $parmValue) {
            $value = is_array($task) ? ($task[$parmName] ?? null) : ($task->$parmName ?? null);
            if (!is_null($value)) {
                $condition &= ($value === $parmValue);
            }
        }
        return $condition;
    }

    private function modifyKeyWithId(array $data): array
    {
        $dataId = [];
        foreach ($data as $task) {
            $id = is_array($task) ? $task['id'] : $task->id;
            $dataId[$id] = $task;
        }
        return $dataId;
    }

    public function fetchByParms(?array $parms = null): array|stdClass|null
    {

        $json = file_get_contents(self::PERSISTANCE_PATH);
        $data = json_decode($json);
        match (true) {
            !file_exists(self::PERSISTANCE_PATH) => throw new RuntimeException('Persistence file not found: ' . self::PERSISTANCE_PATH),
            empty($json) => throw new RuntimeException('Persistence file is empty.'),
            (json_last_error() !== JSON_ERROR_NONE) => throw new RuntimeException('Invalid JSON: ' . json_last_error_msg()),
            default => null
        };

        if (!is_null($parms) && !empty($parms)) {
            $data = array_filter($data, fn($task) => $this->matchByParms($task, $parms));
        }
        $data = $this->modifyKeyWithId($data);
        return (count($data) > 1) ? $data : array_shift($data);
    }

    #[Override]
    public function fetchOne($id)
    {
        return ($this->fetchByParms())[$id] ?? null;
    }
}

<?php

class DataReaderJson extends Model implements Reader
{

    public const PERSISTANCE_PATH = ROOT_PATH . '/app/models/classes/persistanceData.json';
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

    public function fetchByParms(?array $parms): array|stdClass|null
    {
        $json = file_get_contents(self::PERSISTANCE_PATH);
        $data = json_decode($json);
        if (is_null($parms)) {
            return $data;
        }
        $data = array_filter($data, fn($task) => $this->matchByParms($task, $parms));
        if (count($data) > 1) {
            $dataId = [];
            foreach ($data as $task) {
                $id = is_array($task) ? $task['id'] : $task->id;
                $dataId[$id] = $task;
            }
            return $dataId;
        }
        return array_shift($data);
    }
}

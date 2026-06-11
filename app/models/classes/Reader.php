<?php
interface Reader
{
    public function fetchOne(string $id);
    public function fetchByParms(?array $parms): array|Task;
}

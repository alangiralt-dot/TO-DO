<?php
require 'Status.php';
class Task
{
    private Status $status;
    private DateTime $startTime;
    private DateTime $endTime;

    public function __construct(
        private string $id,
        private string $description,
        string $statusValue,
        string $startTimeValue,
        string $endTimeValue,
        private string $createdBy
    ) {
        $this->status = Status::from($statusValue);
        $this->startTime = DateTime::createFromFormat('H:i', $startTimeValue, new DateTimeZone(date_default_timezone_get()));
        $this->endTime = DateTime::createFromFormat('H:i', $endTimeValue, new DateTimeZone(date_default_timezone_get()));
    }

    public function getStatus(): string
    {
        return $this->status->value;
    }

    public function getStatusEnum(): Status
    {
        return $this->status;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStartTime(): string
    {
        return $this->startTime->format('H:i');
    }

    public function getEndTime(): string
    {
        return $this->endTime->format('H:i');
    }

    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }

    public function setStatus(string $statusValue)
    {
        $this->status = Status::from($statusValue);
    }

    public function setId(string $id)
    {
        $this->id = $id;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    public function setStartTime(string $startTimeValue)
    {
        $this->startTime = DateTime::createFromFormat('H:i', $startTimeValue, new DateTimeZone(date_default_timezone_get()));
    }

    public function setEndTime(string $endTimeValue)
    {
        $this->endTime = DateTime::createFromFormat('H:i', $endTimeValue, new DateTimeZone(date_default_timezone_get()));
    }

    public function setCreatedBy(string $createdBy)
    {
        $this->createdBy = $createdBy;
    }

    public function getArray(): array
    {
        return array(
            'id' => $this->getId(),
            'description' => $this->getDescription(),
            'status' => $this->getStatus(),
            'start_time' => $this->getStartTime(),
            'end_time' => $this->getEndTime(),
            'created_by' => $this->getCreatedBy()
        );
    }
}

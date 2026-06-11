<?php
require 'Status.php';
class Task
{
    private Status $status;
    private DateTime $to;
    private DateTime $from;

    public function __construct(
        private string $id,
        private string $description,
        string $statusValue,
        string $startTimeValue,
        string $endTimeValue,
        private string $createdBy
    ) {
        $this->status = Status::from($statusValue);
        $this->from = DateTime::createFromFormat('H:i', $startTimeValue, new DateTimeZone(date_default_timezone_get()));
        $this->to = DateTime::createFromFormat('H:i', $endTimeValue, new DateTimeZone(date_default_timezone_get()));
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

    public function getTo(): string
    {
        return $this->to->format('H:i');
    }

    public function getFrom(): string
    {
        return $this->from->format('H:i');
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

    public function setTo(string $startTimeValue)
    {
        $this->to = DateTime::createFromFormat('H:i', $startTimeValue, new DateTimeZone(date_default_timezone_get()));
    }

    public function setFrom(string $endTimeValue)
    {
        $this->from = DateTime::createFromFormat('H:i', $endTimeValue, new DateTimeZone(date_default_timezone_get()));
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
            'end_time' => $this->getFrom(),
            'start_time' => $this->getTo(),
            'created_by' => $this->getCreatedBy()
        );
    }
}

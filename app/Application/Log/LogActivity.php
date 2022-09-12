<?php
namespace App\Application\Log;


class LogActivity
{
    public int $audit_logable_id;

    public string $audit_logable_type;

    public string $level;

    public string $logged_at;

    public string $message;

    public ?array $context;

    public function __construct(int $audit_logable_id,
                                string $audit_logable_type,
                                string $level,
                                string $logged_at,
                                string $message,
                                ?array $context)
    {
        $this->audit_logable_id = $audit_logable_id;
        $this->audit_logable_type = $audit_logable_type;
        $this->level = $level;
        $this->logged_at = $logged_at;
        $this->message = $message;
        $this->context = $context;
    }

    public function getAuditLogableId(): int
    {
        return $this->audit_logable_id;
    }

    public function setAuditLogableId(int $audit_logable_id): void
    {
        $this->audit_logable_id = $audit_logable_id;
    }

    public function getAuditLogableType(): string
    {
        return $this->audit_logable_type;
    }

    public function setAuditLogableType(string $audit_logable_type): void
    {
        $this->audit_logable_type = $audit_logable_type;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function setLevel(string $level): void
    {
        $this->level = $level;
    }

    public function getLoggedAt(): string
    {
        return $this->logged_at;
    }

    public function setLoggedAt(string $logged_at): void
    {
        $this->logged_at = $logged_at;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getContext(): ?array
    {
        return $this->context;
    }

    public function setContext(?array $context): void
    {
        $this->context = $context;
    }
}

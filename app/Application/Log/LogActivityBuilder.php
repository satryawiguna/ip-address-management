<?php
namespace App\Application\Log;


class LogActivityBuilder
{
    public int $audit_logable_id;

    public string $audit_logable_type;

    public string $level;

    public string $logged_at;

    public string $message;

    public ?array $context;

    public function setAuditLogableId(int $audit_logable_id)
    {
        $this->audit_logable_id = $audit_logable_id;

        return $this;
    }

    public function setAuditLogableType(string $audit_logable_type)
    {
        $this->audit_logable_type = $audit_logable_type;

        return $this;
    }

    public function setLevel(string $level)
    {
        $this->level = $level;

        return $this;
    }

    public function setLoggedAt(string $logged_at)
    {
        $this->logged_at = $logged_at;

        return $this;
    }

    public function setMessage(string $message)
    {
        $this->message = $message;

        return $this;
    }

    public function setContext(?array $context)
    {
        $this->context = $context;

        return $this;
    }

    public function build(): LogActivity
    {
        return new LogActivity(
            $this->audit_logable_id,
            $this->audit_logable_type,
            $this->level,
            $this->logged_at,
            $this->message,
            $this->context
        );
    }
}

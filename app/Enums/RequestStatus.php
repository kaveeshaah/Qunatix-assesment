<?php

namespace App\Enums;

enum RequestStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Returned = 'returned';
    case Assigned = 'assigned';
    case InProgress = 'in_progress';
    case Completed = 'completed';

    /** @return list<self> */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Draft, self::Returned => [self::Submitted],
            self::Submitted => [self::Approved, self::Rejected, self::Returned],
            self::Approved => [self::Assigned],
            self::Assigned => [self::InProgress],
            self::InProgress => [self::Completed],
            self::Rejected, self::Completed => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedTransitions(), true);
    }

    public function isFinal(): bool
    {
        return $this->allowedTransitions() === [];
    }

    public function isEditable(): bool
    {
        return $this === self::Draft || $this === self::Returned;
    }
}
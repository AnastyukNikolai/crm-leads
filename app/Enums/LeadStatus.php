<?php

namespace App\Enums;

enum LeadStatus: string
{
    case New = 'new';
    case InProgress = 'in_progress';
    case Won = 'won';
    case Lost = 'lost';

    public function isTerminal(): bool
    {
        return in_array($this, [self::Won, self::Lost], true);
    }
}

<?php

namespace App\Enums;

enum ProjectTaskStatus: string
{
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Done = 'done';

    public function label(): string
    {
        return match ($this) {
            self::Todo => 'Por hacer',
            self::InProgress => 'En progreso',
            self::Done => 'Hecho',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Todo => 'gray',
            self::InProgress => 'amber',
            self::Done => 'green',
        };
    }
}

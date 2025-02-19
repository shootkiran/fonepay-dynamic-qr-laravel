<?php

namespace ShootKiran\DynamicQrGeneratorFonepay\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;


enum FonepayStatus: string implements HasLabel, HasIcon, HasColor
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::SUCCESS => 'Success',
            self::FAILED => 'Failed',
        };
    }
    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning', // Yellow
            self::SUCCESS => 'success', // Green
            self::FAILED => 'danger', // Red
        };
    }
    public function getIcon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
            self::SUCCESS => 'heroicon-o-check-circle',
            self::FAILED => 'heroicon-o-x-circle',
        };
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['status'])]
class TicketStatus extends Model
{
    /** @var list<array{light: string, dark: string}> */
    private const COLOR_PALETTE = [
        ['light' => '#1D4ED8', 'dark' => '#93C5FD'],
        ['light' => '#047857', 'dark' => '#6EE7B7'],
        ['light' => '#B45309', 'dark' => '#FCD34D'],
        ['light' => '#7E22CE', 'dark' => '#D8B4FE'],
        ['light' => '#BE185D', 'dark' => '#F9A8D4'],
        ['light' => '#0E7490', 'dark' => '#67E8F9'],
        ['light' => '#C2410C', 'dark' => '#FDBA74'],
        ['light' => '#0F766E', 'dark' => '#5EEAD4'],
        ['light' => '#4338CA', 'dark' => '#A5B4FC'],
        ['light' => '#4D7C0F', 'dark' => '#BEF264'],
        ['light' => '#A16207', 'dark' => '#FDE047'],
        ['light' => '#BE123C', 'dark' => '#FDA4AF'],
    ];

    protected $table = 'ticket_status';

    /** @return array{light: string, dark: string} */
    public function palette(): array
    {
        $index = $this->getKey() === null
            ? self::paletteIndexFor($this->status)
            : ((int) $this->getKey() - 1) % count(self::COLOR_PALETTE);

        return self::COLOR_PALETTE[$index];
    }

    /** @return array{light: string, dark: string} */
    public static function paletteFor(string $status): array
    {
        return self::COLOR_PALETTE[self::paletteIndexFor($status)];
    }

    public function description(): string
    {
        $statusKey = strtolower(str_replace(' ', '-', trim($this->status)));

        return match ($statusKey) {
            'new' => 'Ticket was newly submitted',
            'open' => 'Ticket is open and awaiting review',
            'in-progress' => 'Support team is working on the ticket',
            'resolved' => 'The reported issue has been resolved',
            'closed' => 'Ticket is closed',
            'hold' => 'Ticket is temporarily on hold',
            'no-helpdesk' => 'Client has no Helpdesk coverage',
            'no-maintenance' => 'Client has no active maintenance',
            'pending-from-client' => 'Awaiting information from the client',
            'pending-from-dev' => 'Awaiting action from Development',
            'pending-from-sales' => 'Awaiting action from Sales',
            'pending-from-xti' => 'Awaiting internal action from XTI',
            default => 'Current status: '.ucwords(str_replace('-', ' ', $this->status)),
        };
    }

    private static function paletteIndexFor(string $status): int
    {
        return (int) (hexdec(substr(hash('sha256', strtolower(trim($status))), 0, 8)) % count(self::COLOR_PALETTE));
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}

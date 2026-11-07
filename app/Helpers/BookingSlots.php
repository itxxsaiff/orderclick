<?php

namespace App\Helpers;

use App\Models\Booking;
use App\Models\BookingService;
use App\Models\Doctor;
use App\Models\Timing;
use Carbon\Carbon;

/**
 * Time slots for booking stores, built from the store's opening hours (Settings > Working hours)
 * and the length of the chosen service. A slot is free when the chosen team member has no
 * overlapping booking — or, when the customer picks "any", while fewer bookings overlap it than
 * the store has team members (at least one).
 */
class BookingSlots
{
    /** Booking status 4 = cancelled; cancelled bookings free their slot again. */
    private const CANCELLED = 4;

    /** "45 min", "1 hour", "1.5 hours", "90", "٤٥ دقيقة", "ساعة" -> minutes (default 30). */
    public static function minutes(?string $duration): int
    {
        $d = mb_strtolower(trim((string) $duration));
        if ($d === '') {
            return 30;
        }
        $d = strtr($d, ['٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9', '٫' => '.']);
        $num = preg_match('/(\d+(?:[.,]\d+)?)/', $d, $m) ? (float) str_replace(',', '.', $m[1]) : null;
        $isHours = (bool) preg_match('/(hour|hr|\bh\b|ساع|stunde|heure)/u', $d);
        if ($num === null) {
            $minutes = $isHours ? 60 : 30;
        } else {
            $minutes = $isHours ? $num * 60 : $num;
        }

        return (int) max(10, min(600, round($minutes)));
    }

    /** Does this store have any opening hours set up? Without them the time is typed freely. */
    public static function configured($vendorId): bool
    {
        return Timing::where('vendor_id', $vendorId)->exists();
    }

    /**
     * Free start times for one day: [['time' => '09:00', 'label' => '9:00 AM'], ...].
     * $staff is a team member id (or null for "any available").
     */
    public static function free($vendorId, string $date, $serviceId = null, $staff = null): array
    {
        try {
            $day = Carbon::createFromFormat('!Y-m-d', $date);
        } catch (\Throwable $e) {
            return [];
        }
        if ($day->lt(now()->startOfDay())) {
            return [];
        }

        $timing = Timing::where('vendor_id', $vendorId)->where('day', $day->format('l'))->first();
        if (!$timing || (int) $timing->is_always_close === 1) {
            return [];
        }
        $open = self::at($day, $timing->open_time);
        $close = self::at($day, $timing->close_time);
        if (!$open || !$close || $close->lte($open)) {
            return [];
        }
        $breakStart = self::at($day, $timing->break_start);
        $breakEnd = self::at($day, $timing->break_end);
        $hasBreak = $breakStart && $breakEnd && $breakEnd->gt($breakStart);

        $length = self::minutes(optional(BookingService::where('vendor_id', $vendorId)->find($serviceId))->duration);

        // Existing bookings of that day, with their own lengths.
        $durations = BookingService::where('vendor_id', $vendorId)->pluck('duration', 'id');
        $busy = Booking::where('vendor_id', $vendorId)->where('booking_date', $day->toDateString())
            ->where('status', '!=', self::CANCELLED)->whereNotNull('booking_time')->where('booking_time', '!=', '')
            ->get(['service_id', 'booking_time', 'staff'])
            ->map(function ($b) use ($day, $durations) {
                $start = self::at($day, $b->booking_time);
                return $start ? [
                    'start' => $start,
                    'end' => $start->copy()->addMinutes(self::minutes($durations[$b->service_id] ?? null)),
                    'staff' => mb_strtolower(trim((string) $b->staff)),
                ] : null;
            })->filter()->values();

        $team = Doctor::where('vendor_id', $vendorId)->where('is_available', 1)->get(['id', 'name']);
        $member = $staff ? $team->firstWhere('id', (int) $staff) : null;
        $capacity = max(1, $team->count());

        $slots = [];
        $earliest = now()->addMinutes(15);
        for ($t = $open->copy(); $t->copy()->addMinutes($length)->lte($close); $t->addMinutes($length)) {
            $end = $t->copy()->addMinutes($length);
            if ($t->lt($earliest)) {
                continue;
            }
            if ($hasBreak && $t->lt($breakEnd) && $end->gt($breakStart)) {
                continue;
            }
            $overlapping = $busy->filter(fn($b) => $b['start']->lt($end) && $b['end']->gt($t));
            if ($member) {
                $taken = $overlapping->contains(fn($b) => $b['staff'] === mb_strtolower(trim($member->name)));
            } else {
                $taken = $overlapping->count() >= $capacity;
            }
            if (!$taken) {
                $slots[] = ['time' => $t->format('H:i'), 'label' => $t->format('g:i A')];
            }
            if (count($slots) > 200) {
                break;
            }
        }

        return $slots;
    }

    /** Is this start time still free? Used again when the booking is saved. */
    public static function isFree($vendorId, string $date, string $time, $serviceId = null, $staff = null): bool
    {
        return collect(self::free($vendorId, $date, $serviceId, $staff))->contains('time', $time);
    }

    /** "09:00 AM" / "09:00" on the given day, or null. */
    private static function at(Carbon $day, $time): ?Carbon
    {
        $time = trim((string) $time);
        if ($time === '') {
            return null;
        }
        foreach (['h:i A', 'g:i A', 'H:i', 'H:i:s', 'h:iA'] as $format) {
            try {
                $parsed = Carbon::createFromFormat('!Y-m-d ' . $format, $day->toDateString() . ' ' . $time);
                if ($parsed !== false) {
                    return $parsed;
                }
            } catch (\Throwable $e) {
                // try the next format
            }
        }

        return null;
    }
}

<?php

namespace App\Filament\Widgets;

use App\Enums\MeetingStatuses;
use App\Models\Meetings\Meeting;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class MeetingsChart extends ChartWidget
{
    protected static ?string $heading = 'Meetings';
    protected static ?string $pollingInterval = null; // Avoid refreshing the chart data
    public ?string $filter = 'this_week';
    public $start_value;
    public $end_value;
    public $extra_description;

    protected function getData(): array
    {
        $today = now();

        $base_query = Meeting::whereHas('meeting_users');

        $cancelled_meetings = Trend::query($base_query->clone()->where('status', MeetingStatuses::CANCELLED))->dateColumn('meeting_date');
        $completed_meetings = Trend::query($base_query->clone()->where('status', MeetingStatuses::COMPLETED))->dateColumn('meeting_date');
        $no_show_meetings = Trend::query($base_query->clone()->where('status', MeetingStatuses::NO_SHOW))->dateColumn('meeting_date');
        $pending_meetings = Trend::query($base_query->clone()->where('status', MeetingStatuses::PENDING))->dateColumn('meeting_date');

        if (in_array($this->filter, ['last_week', 'this_week', 'last_month', 'this_month'])) {
            if ($this->filter == 'last_week') {
                $this->start_value = $today->copy()->subWeek(1)->startOfWeek();
                $this->end_value = $today->copy()->subWeek(1)->endOfWeek();
            } else if ($this->filter == 'last_month') {
                $days_of_previous_month = $today->copy()->subMonth(1)->daysInMonth;

                $this->start_value = $today->copy()->subDays($days_of_previous_month)->startOfMonth();
                $this->end_value = $today->copy()->subDays($days_of_previous_month)->endOfMonth();
            } else {
                if ($this->filter == 'this_week') {
                    $this->start_value = $today->copy()->startOfWeek();
                    $this->end_value = $today->copy()->endOfWeek();
                } else if ($this->filter == 'this_month') {
                    $this->start_value = $today->copy()->startOfMonth();
                    $this->end_value = $today->copy()->endOfMonth();
                }
            }

            $cancelled_meetings = $cancelled_meetings->between(start: $this->start_value, end: $this->end_value)
                ->perDay();
            $completed_meetings = $completed_meetings->between(start: $this->start_value, end: $this->end_value)
                ->perDay();
            $no_show_meetings = $no_show_meetings->between(start: $this->start_value, end: $this->end_value)
                ->perDay();
            $pending_meetings = $pending_meetings->between(start: $this->start_value, end: $this->end_value)
                ->perDay();

            $period_iterator = 'day';

            if (in_array($this->filter, ['last_week', 'this_week'])) {
                $this->extra_description = 'between ' .$this->start_value->format('M d, Y'). ' - ' .$this->end_value->format('M d, Y'). '.';
            } else if (in_array($this->filter, ['last_month', 'this_month'])) {
                $this->extra_description = 'for ' .$this->start_value->format('F Y'). '.';
            }
        } else if (in_array($this->filter, ['last_year', 'this_year'])) {
            $this->start_value = $today->copy()->startOfYear();
            $this->end_value = $today->copy()->endOfYear();

            if ($this->filter == 'last_year') {
                $this->start_value = $this->start_value->copy()->subYear(1);
                $this->end_value = $this->end_value->copy()->subYear(1);
            }

            $cancelled_meetings = $cancelled_meetings->between(start: $this->start_value, end: $this->end_value)
                ->perMonth();
            $completed_meetings = $completed_meetings->between(start: $this->start_value, end: $this->end_value)
                ->perMonth();
            $no_show_meetings = $no_show_meetings->between(start: $this->start_value, end: $this->end_value)
                ->perMonth();
            $pending_meetings = $pending_meetings->between(start: $this->start_value, end: $this->end_value)
                ->perMonth();

            $period_iterator = 'month';
            $this->extra_description = 'per month for ' .$this->start_value->format('Y'). '.';
        }

        $cancelled_meetings = $cancelled_meetings->count();
        $completed_meetings = $completed_meetings->count();
        $no_show_meetings = $no_show_meetings->count();
        $pending_meetings = $pending_meetings->count();

        $period = CarbonPeriod::create($this->start_value, '1 ' .$period_iterator, $this->end_value);
        $labels = [];

        foreach ($period as $date) {
            if (in_array($this->filter, ['last_week', 'this_week'])) {
                $labels[] = $date->format('M d');
            } else if (in_array($this->filter, ['last_month', 'this_month'])) {
                $labels[] = $date->format('d');
            } else if (in_array($this->filter, ['last_year', 'this_year'])) {
                $labels[] = $date->format('M Y');
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Cancelled',
                    'data' => $cancelled_meetings->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#f87171', // danger-400
                    'backgroundColor' => '#fef2f2', // danger-50
                ],
                [
                    'label' => 'Completed',
                    'data' => $completed_meetings->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#4ade80', // success-400
                    'backgroundColor' => '#f0fdf4', // success-50
                ],
                [
                    'label' => 'No Show',
                    'data' => $no_show_meetings->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#a1a1aa', // gray-400
                    'backgroundColor' => '#fafafa', // gray-50
                ],
                [
                    'label' => 'Pending',
                    'data' => $pending_meetings->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#fbbf24', // warning-400
                    'backgroundColor' => '#fffbeb', // warning-50
                ],
            ],
            'labels' => $labels
        ];
    }

    public function getColumnSpan(): int|string|array
    {
        return 2;
    }

    public function getDescription(): ?string
    {
        return 'Meetings ' .$this->extra_description;
    }

    protected function getFilters(): ?array
    {
        return [
            'last_year' => 'Last year',
            'this_year' => 'This year',
            'last_month' => 'Last month',
            'this_month' => 'This month',
            'last_week' => 'Last week',
            'this_week' => 'This week'
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

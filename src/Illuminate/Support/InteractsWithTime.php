<?php

namespace Illuminate\Support;

use Carbon\CarbonInterval;
use DateInterval;
use DateTimeInterface;
use Time\Duration;

trait InteractsWithTime
{
    /**
     * Get the number of seconds until the given DateTime.
     *
     * @param  \DateTimeInterface|\DateInterval|\Time\Duration|int  $delay
     * @return int
     */
    protected function secondsUntil($delay)
    {
        $delay = $this->parseDateInterval($delay);

        return $delay instanceof DateTimeInterface
            ? max(0, $delay->getTimestamp() - $this->currentTime())
            : (int) $delay;
    }

    /**
     * Get the "available at" UNIX timestamp.
     *
     * @param  \DateTimeInterface|\DateInterval|\Time\Duration|int  $delay
     * @return int
     */
    protected function availableAt($delay = 0)
    {
        $delay = $this->parseDateInterval($delay);

        return $delay instanceof DateTimeInterface
            ? $delay->getTimestamp()
            : Carbon::now()->addSeconds($delay)->getTimestamp();
    }

    /**
     * If the given value is an interval, convert it to a DateTime instance.
     *
     * @param  \DateTimeInterface|\DateInterval|\Time\Duration|int  $delay
     * @return \DateTimeInterface|int
     */
    protected function parseDateInterval($delay)
    {
        if ($delay instanceof Duration) {
            $delay = $this->durationToInterval($delay);
        }

        if ($delay instanceof DateInterval) {
            $delay = Carbon::now()->add($delay);
        }

        return $delay;
    }

    /**
     * Convert the given duration to a Carbon interval.
     *
     * @param  \Time\Duration  $duration
     * @return \Carbon\CarbonInterval
     */
    protected function durationToInterval($duration)
    {
        return CarbonInterval::seconds($duration->seconds)
            ->microseconds(intdiv($duration->nanoseconds, 1000))
            ->invert($duration->negative);
    }

    /**
     * Get the current system time as a UNIX timestamp.
     *
     * @return int
     */
    protected function currentTime()
    {
        return Carbon::now()->getTimestamp();
    }

    /**
     * Given a start time, format the total run time for human readability.
     *
     * @param  float  $startTime
     * @param  float|null  $endTime
     * @return string
     */
    protected function runTimeForHumans($startTime, $endTime = null)
    {
        $endTime ??= microtime(true);

        $runTime = ($endTime - $startTime) * 1000;

        return $runTime > 1000
            ? CarbonInterval::milliseconds($runTime)->cascade()->forHumans(short: true)
            : number_format($runTime, 2).'ms';
    }
}

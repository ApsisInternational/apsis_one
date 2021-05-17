<?php

namespace Apsis\One\Helper;

use DateTime;
use DateTimeZone;
use Exception;
use DateInterval;

class DateHelper extends LoggerHelper
{
    /**
     * @param string|null $date
     * @param string $format
     *
     * @return string
     */
    public function formatDateForPlatformCompatibility(?string $date = null, string $format = self::TIMESTAMP): string
    {
        try {
            if (empty($date)) {
                $date = 'now';
            }

            return $this->getDateTimeFromTime($date)->format($format);
        } catch (Exception $e) {
            $this->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return '';
        }
    }

    /**
     * @param string|null $date
     * @param string $format
     *
     * @return string
     */
    public function addSecond(?string $date = null, string $format = self::TIMESTAMP): string
    {
        try {
            if (empty($date)) {
                $date = 'now';
            }

            return $this->getDateTimeFromTime($date)
                ->add($this->getDateIntervalFromIntervalSpec('PT1S'))
                ->format($format);
        } catch (Exception $e) {
            $this->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return '';
        }
    }

    /**
     * @param string $inputDateTime
     * @param int $day
     *
     * @return string
     */
    public function getFormattedDateTimeWithAddedInterval(string $inputDateTime, int $day = 1): string
    {
        try {
            return $this->getDateTimeFromTimeAndTimeZone($inputDateTime)
                ->add($this->getDateIntervalFromIntervalSpec(sprintf('P%sD', $day)))
                ->format(self::ISO_8601);
        } catch (Exception $e) {
            $this->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return '';
        }
    }

    /**
     * @param string $inputDateTime
     *
     * @return bool
     */
    public function isExpired(string $inputDateTime): bool
    {
        try {
            $nowDateTime = $this->getDateTimeFromTimeAndTimeZone()->format(self::ISO_8601);
            return ($nowDateTime > $inputDateTime);
        } catch (Exception $e) {
            $this->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param string $time
     * @param string $timezone
     *
     * @return DateTime
     *
     * @throws Exception
     */
    public function getDateTimeFromTimeAndTimeZone(string $time = 'now', string $timezone = 'UTC'): DateTime
    {
        return new DateTime($time, new DateTimeZone($timezone));
    }

    /**
     * @param string $time
     *
     * @return DateTime
     *
     * @throws Exception
     */
    public function getDateTimeFromTime(string $time = 'now'): DateTime
    {
        return new DateTime($time);
    }

    /**
     * @param string $intervalSpec
     *
     * @return DateInterval
     *
     * @throws Exception
     */
    public function getDateIntervalFromIntervalSpec(string $intervalSpec): DateInterval
    {
        return new DateInterval($intervalSpec);
    }
}
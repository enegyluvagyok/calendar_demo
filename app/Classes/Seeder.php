<?php

namespace App\Classes;

use App\Models\CalendarEvent;
use Symfony\Component\HttpFoundation\Request;

class Seeder
{

    public function customEventsSeeder(Request $request)
    {

        $noLabourDays = self::getNoLabourDaysInRange($request->start, $request->end);
        $data = self::setLabelsForNoLabourDays($noLabourDays);

        foreach ($data as $item) {
            CalendarEvent::create($item);
        }
    }

    /** 2022-11-25 + 2024-11-25 */

    public function getNoLabourDaysInRange(String $startDate, String $endDate)
    {

        if (!defined('SATURDAY')) { define('SATURDAY', 6); }
        if (!defined('SUNDAY')) { define('SUNDAY', 0); }

        $fixedNoLabourDays = array('01-01', '03-15', '05-01', '08-20', '10-23', '11-01', '12-25', '12-26');

        $yearStart = date('Y', strtotime($startDate));
        $yearEnd   = date('Y', strtotime($endDate));

        for ($i = $yearStart; $i <= $yearEnd; $i++) {
            $noLabourDays[] = date('Y-m-d', easter_date($i) + 86400); //easter-monday
            $noLabourDays[] = date('Y-m-d', easter_date($i) + 4320000); //whitmonday
            $noLabourDays[] = date('Y-m-d', easter_date($i) - 172800); //goodfriday
        }

        $startTimestamp = strtotime($startDate);
        $endTimestamp   = strtotime($endDate);

        for ($i = $startTimestamp; $i <= $endTimestamp; $i = strtotime("+1 day", $i)) {
            $day = date("w", $i);
            $mmgg = date('m-d', $i);
            if ($day == SUNDAY || $day == SATURDAY || in_array($mmgg, $fixedNoLabourDays)) {
                $noLabourDays[] = date('Y-m-d', $i);
            }
        }

        sort($noLabourDays);
        return $noLabourDays;
    }

    public function setLabelsForNoLabourDays(array $noLabourDays)
    {

        if (!defined('SATURDAY')) { define('SATURDAY', 6); }
        if (!defined('SUNDAY')) { define('SUNDAY', 0); }

        $namedHolidays = array(
            '01-01' => 'Újév',
            '03-15' => 'Március 15.',
            '05-01' => 'A munka ünnepe',
            '08-20' => 'Államalapítás',
            '10-23' => '1956-os forradalom',
            '11-01' => 'Halottak napja',
            '12-25' => 'Karácsony',
            '12-26' => 'Karácsony'
        );

        foreach ($noLabourDays as $noLabourday) {

            $note = '';
            $color = 'red';

            if (date('w', strtotime($noLabourday)) == SATURDAY) {
                $note .= 'szombat';
                $color = 'red';
            }
            if (date('w', strtotime($noLabourday)) == SUNDAY) {
                $note .= 'vasárnap';
            }
            if (array_key_exists(date('m-d', strtotime($noLabourday)), $namedHolidays)) {
                if ($note == '') {
                    $note .= $namedHolidays[date('m-d', strtotime($noLabourday))];
                } else {
                    $note .= ', ' . $namedHolidays[date('m-d', strtotime($noLabourday))];
                }
            }
            if (strtotime($noLabourday) == easter_date(date('Y', strtotime($noLabourday)))) {
                $note .= ', Húsvét';
            }
            if (strtotime($noLabourday) == easter_date(date('Y', strtotime($noLabourday))) + 86400) {
                $note .= 'Húsvét';
            }
            if (strtotime($noLabourday) == easter_date(date('Y', strtotime($noLabourday))) + 4233600) {
                $note .= ', Pünkösd';
            }
            if (strtotime($noLabourday) == easter_date(date('Y', strtotime($noLabourday))) + 4320000) {
                $note .= 'Pünkösd';
            }
            if (strtotime($noLabourday) == easter_date(date('Y', strtotime($noLabourday))) - 172800) {
                $note .= 'Nagypéntek';
            }

            $item['start'] = $noLabourday;
            $item['end'] = $noLabourday;
            $item['note'] = $note;
            $item['name'] = 'Pihenő vagy ünnepnap';
            $item['color'] = $color;
            $item['status'] = 0;
            $items[] = $item;
        }
        return $items;
    }
}

<?php

class RSCString {

	public static function generateRandomString($length = 15) {
		return substr(sha1(rand()), 0, $length);
	}

	/**
	 * 
	 * @param type $events
	 * 
	 * 
	  BEGIN:VEVENT
	  DTSTART:20110909T083000Z
	  DTEND:20110909T103000Z
	  DTSTAMP:20110722T004312Z
	  UID:e5fhdjff6vakjftnl3l9vjs64k@google.com
	  CREATED:20110721T105410Z
	  DESCRIPTION:
	  LAST-MODIFIED:20110721T111008Z
	  LOCATION:Auckland
	  SEQUENCE:1
	  STATUS:CONFIRMED
	  SUMMARY:Insert something
	  TRANSP:OPAQUE
	  BEGIN:VALARM
	  ACTION:DISPLAY
	  DESCRIPTION:Insert something
	  TRIGGER:-P0DT0H10M0S
	  END:VALARM
	  END:VEVENT
	 */
	public static function ical_encode($events) {
		$result = RSCString::ical_head();
		$result .= RSCString::ical_tz_est();
		foreach ($events as $event) {
			$result .= RSCString::ical_event($event);
		}
		$result .= RSCString::ical_foot();

		return $result;
	}

	private static function ical_head() {
		$result = "BEGIN:VCALENDAR\r\n";
		$result .= "PRODID:-//GenKro//masterKal 1.0//EN\r\n";
		$result .= "VERSION:2.0\r\n";
		$result .= "CALSCALE:GREGORIAN\r\n";
		$result .= "METHOD:PUBLISH\r\n";
		$result .= "X-WR-CALNAME:masterKal\r\n";
		$result .= "X-WR-TIMEZONE:\r\n";
		$result .= "X-WR-CALDESC:\r\n";

		return $result;
	}

	private static function ical_tz_est() {
		$result = "BEGIN:VTIMEZONE\r\n";
		$result .= "TZID:US-Eastern\r\n";
		$result .= "LAST-MODIFIED:19870101T000000Z\r\n";
		$result .= "TZURL:http://zones.stds_r_us.net/tz/US-Eastern\r\n";
		$result .= "BEGIN:STANDARD\r\n";
		$result .= "DTSTART:19671029T020000\r\n";
		$result .= "RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=10\r\n";
		$result .= "TZOFFSETFROM:-0400\r\n";
		$result .= "TZOFFSETTO:-0500\r\n";
		$result .= "TZNAME:EST\r\n";
		$result .= "END:STANDARD\r\n";
		$result .= "BEGIN:DAYLIGHT\r\n";
		$result .= "DTSTART:19870405T020000\r\n";
		$result .= "RRULE:FREQ=YEARLY;BYDAY=1SU;BYMONTH=4\r\n";
		$result .= "TZOFFSETFROM:-0500\r\n";
		$result .= "TZOFFSETTO:-0400\r\n";
		$result .= "TZNAME:EDT\r\n";
		$result .= "END:DAYLIGHT\r\n";
		$result .= "END:VTIMEZONE\r\n";

		return $result;
	}

	private static function ical_event($event) {
		$format = "Ymd\THis";
		$formatAllDay = "Ymd";

		$result = "BEGIN:VEVENT\r\n";
		$result .= "DTSTART" . ($event['allDay'] == true ? ";VALUE=DATE:" : ":");
		//$result .= ($event['allDay'] == true) ? ";VALUE=DATE:" : ":"; 
		$result .= date($event['allDay'] == true ? $formatAllDay : $format, strtotime($event['startDate'] . ' ' . $event['startTime'] . ' ' . $event['timeZone'])) . "\r\n";
		$result .= "DTEND" . ($event['allDay'] == true ? ";VALUE=DATE:" : ":"); 
		$result .= date($event['allDay'] == true ? $formatAllDay : $format, strtotime($event['endDate'] . ' ' . $event['endTime'] . ' ' . $event['timeZone'])) . "\r\n";
		$result .= "DTSTAMP:" . date($format) . "\r\n";
		$result .= "UID:" . $event['eventId'] . "@masterkal.com\r\n";
		$result .= "CREATED:" . date($format, strtotime($event['createTime'])) . "\r\n";
		//$result .= "DESCRIPTION:" . (!empty($event['location']) ? "https://www.google.com/maps/search/" . $event['location'] . "\\n" : "") . $event['description'] . "\n";
		$result .= "DESCRIPTION:" . $event['description'] . "\r\n";
		$result .= "LAST-MODIFIED:" . date($format, strtotime($event['modifyTime'])) . "\r\n";
		$result .= "LOCATION:" . str_replace(",", "\,", $event['location']) . "\r\n";
		$result .= "SEQUENCE:1\r\n";
		$result .= "STATUS:CONFIRMED\r\n";
		$result .= "SUMMARY:" . ($event['prefix'] ? $event['prefix'] . " - " : "") . $event['name'] . "\r\n";
		$result .= "TRANSP:OPAQUE\r\n";
		$result .= "END:VEVENT\r\n";

		return $result;
	}

	private static function ical_foot() {
		$result = "END:VCALENDAR";

		return $result;
	}

}

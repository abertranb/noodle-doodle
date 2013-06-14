<?php
/**
* @author decima
* The purpose of the doodleData class is to generate all the xml datas used to create/update informations on Doodle
*/
class DoodleData {

    const XMLNS = "http://doodle.com/xsd1";
    const DOODLE_TYPE_TEXT = "TEXT";
    const DOODLE_TYPE_DATE = "DATE";

    public static function createDateOption($timestamp_start, $use_time = false, $timestamp_end = null) {
        $ret = array();
        if ($timestamp_start && $timestamp_end && !$use_time) {
            throw new Exception("Enable to set a date interval");
        }
        $suffix = $use_time ? "datetime" : "date";
        $format = $use_time ? "Y-m-d\TH:i:00" : "Y-m-d";
        if ($timestamp_end == null) {
            $ret[$suffix] = date($format, $timestamp_start);
        } else {
            $ret["start" . $suffix] = date($format, $timestamp_start);
            $ret["end" . $suffix] = date($format, $timestamp_end);
        }
        return $ret;
    }

    public static function createInitiator($name, $email = null) {
        $initiator = array();
        $initiator['name'] = $name;
        if ($email) {
            $initiator['email'] = $email;
        }
        return $initiator;
    }

    public static function writePoll($type, $is_visible, $title, $description, $options, $initiator) {
        $xml = self::start_xml("poll");
        $xml->startElement("type");
        $xml->writeRaw($type);
        $xml->endElement();
        $xml->startElement("hidden");
        $xml->writeRaw($is_visible ? "true" : "false");
        $xml->endElement();
        $xml->startElement("levels");
        $xml->writeRaw("2");
        $xml->endElement();
        $xml->startElement("title");
        $xml->writeRaw($title);
        $xml->endElement();
        $xml->startElement("description");
        $xml->writeRaw($description);
        $xml->endElement();
        $xml->startElement("initiator");
        if (is_array($initiator)) {
            if (isset($initiator['name'])) {
                $xml->startElement("name");
                $xml->writeRaw($initiator['name']);
                $xml->endElement();
            }
            if (isset($initiator['email'])) {
                $xml->startElement("eMailAddress");
                $xml->writeRaw($initiator['email']);
                $xml->endElement();
            }
        } else {
            $xml->startElement("name");
            $xml->writeRaw($initiator);
            $xml->endElement();
        }

        $xml->endElement();
        $xml->startElement("options");
        switch ($type) {
            case self::DOODLE_TYPE_TEXT:
                foreach ($options as $option) {
                    $xml->startElement("option");
                    $xml->writeRaw($option);
                    $xml->endElement();
                }
                break;
            case self::DOODLE_TYPE_DATE:
                foreach ($options as $option) {
                    $xml->startElement("option");
                    if (isset($option['date']))
                        $xml->writeAttribute('date', $option['date']);
                    if (isset($option['startdate']))
                        $xml->writeAttribute('startDate', $option['startdate']);
                    if (isset($option['enddate']))
                        $xml->writeAttribute('endDate', $option['enddate']);
                    if (isset($option['datetime']))
                        $xml->writeAttribute('dateTime', $option['datetime']);
                    if (isset($option['startdatetime']))
                        $xml->writeAttribute('startDateTime', $option['startdatetime']);
                    if (isset($option['enddatetime']))
                        $xml->writeAttribute('endDateTime', $option['enddatetime']);
                    $xml->endElement();
                }

                break;
        }
        $xml->endElement();

        return self::end_xml($xml);
    }

    public static function writeComment($who, $what) {
        $xml = self::start_xml("comment");
        $xml->startElement("who");
        $xml->writeRaw($who);
        $xml->endElement();
        $xml->startElement("what");
        $xml->writeRaw($what);
        $xml->endElement();
        return self::end_xml($xml);
    }

    public static function writeAnswer($who, $options) {
        $xml = self::start_xml("participant");
        $xml->startElement("name");
        $xml->writeRaw($who);
        $xml->endElement();
        $xml->startElement("preferences");
        foreach ($options as $option) {
            $xml->startElement("option");
            $xml->writeRaw($option);
            $xml->endElement();
        }
        $xml->endElement();
        return self::end_xml($xml);
    }

    private static function end_xml($xml) {
        $xml->endElement();
        return $xml->outputMemory();
    }

    private static function start_xml($start_xml_element) {
        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->setIndentString("    ");
        $xml->startElement($start_xml_element);
        $xml->writeAttribute("xmlns", self::XMLNS);
        return $xml;
    }

}

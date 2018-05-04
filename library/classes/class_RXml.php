<?php

class RXml {

    // ****************************************************************************
    // ****************************************************************************
    // Make Array from XML
    // ****************************************************************************

    public static function getArray($xml) {
        return self::makeArray($xml);
    }

    private function GetChildren($vals, &$i) {
        $children = array();
        if (isset($vals[$i]['value'])) {
            $children['VALUE'] = $vals[$i]['value'];
        }

        while (++$i < count($vals)) {

            switch ($vals[$i]['type']) {

                case 'cdata':
                    if (isset($children['VALUE'])) {
                        $children['VALUE'] .= $vals[$i]['value'];
                    } else {
                        $children['VALUE'] = $vals[$i]['value'];
                    }
                    break;

                case 'complete':
                    if (isset($vals[$i]['attributes'])) {
                        $children[$vals[$i]['tag']][]['ATTRIBUTES'] = $vals[$i]['attributes'];
                        $index = count($children[$vals[$i]['tag']]) - 1;

                        if (isset($vals[$i]['value'])) {
                            $children[$vals[$i]['tag']][$index]['VALUE'] = $vals[$i]['value'];
                        } else {
                            $children[$vals[$i]['tag']][$index]['VALUE'] = '';
                        }
                    } else {
                        if (isset($vals[$i]['value'])) {
                            $children[$vals[$i]['tag']][]['VALUE'] = $vals[$i]['value'];
                        } else {
                            $children[$vals[$i]['tag']][]['VALUE'] = '';
                        }
                    }
                    break;

                case 'open':
                    if (isset($vals[$i]['attributes'])) {
                        $children[$vals[$i]['tag']][]['ATTRIBUTES'] = $vals[$i]['attributes'];
                        $index = count($children[$vals[$i]['tag']]) - 1;
                        $children[$vals[$i]['tag']][$index] = array_merge($children[$vals[$i]['tag']][$index], self::GetChildren($vals, $i));
                    } else {
                        $children[$vals[$i]['tag']][] = self::GetChildren($vals, $i);
                    }
                    break;

                case 'close':
                    return $children;
            }
        }
    }

    private function makeArray($xmlfile) {



        if (file_exists($xmlfile)) {
            $data = implode('', file($xmlfile));
        } else {
            $fp = fopen($xmlfile, 'r');

            // Rasmus: Sætter timeout til 360sek på stream
            stream_set_timeout($fp, 360);

            if ($fp) {
                while (!feof($fp)) {
                    $data = $data . fread($fp, 1024);
                }
                fclose($fp);
            }
        }



        $parser = xml_parser_create('ISO-8859-1');
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, $data, $vals, $index);
        xml_parser_free($parser);

        $tree = array();
        $i = 0;

        if (isset($vals[$i]['attributes'])) {
            $tree[$vals[$i]['tag']][]['ATTRIBUTES'] = $vals[$i]['attributes'];
            $index = count($tree[$vals[$i]['tag']]) - 1;

            $tmparray = null;
            $tmparray = self::GetChildren($vals, $i);
            if (is_array($tmparray)) {
                $tree[$vals[$i]['tag']][$index] = array_merge($tree[$vals[$i]['tag']][$index], $tmparray);
            }
        } else {
            $tmparray = null;
            $tmparray = self::GetChildren($vals, $i);
            if (is_array($tmparray)) {
                $tree[$vals[$i]['tag']][] = $tmparray;
            }
        }
        return $tree;
    }

    // ****************************************************************************
    // ****************************************************************************
    // ****************************************************************************
    // Lav XML ud fra Array
    // ****************************************************************************

    function getXml($array, $rootTag = "data") {


        if (is_array($array)) {


            $write = "<?xml version=\"1.0\"  encoding=\"UTF-8\" ?>";
            $write.= "<" . self::xmlTagEncode($rootTag) . ">";

            foreach (array_keys($array) as $ak) {

                $write.= self::writeText($ak, $array[$ak]);
            }

            $write.= "</" . self::xmlTagEncode($rootTag) . ">";

            return $write;
        } else {
            return false;
        }
    }

    private static function writeText($name, $data) {
        $retur = "<" . self::xmlTagEncode($name) . ">";
        if (is_array($data)) {
            foreach (array_keys($data) as $dk) {

                $retur.= self::writeText($dk, $data[$dk]);
            }
        } else {

            $retur.= self::xmlTextEncode($data);
        }

        $retur.= "</" . self::xmlTagEncode($name) . ">";

        return $retur;
    }

    private static function xmlTextEncode($string) {
        $string = str_ireplace("&", "&amp;", $string);
        $string = str_ireplace("<", "&lt;", $string);
        $string = str_ireplace(">", "&gt;", $string);
        $string = str_ireplace('"', "&quot;", $string);
        $string = str_ireplace("'", "&apos;", $string);
        return $string;
    }

    private static function xmlTagEncode($string) {
        $string = str_ireplace("&", "_", $string);
        $string = str_ireplace("<", "_", $string);
        $string = str_ireplace(">", "_", $string);
        $string = str_ireplace('"', "_", $string);
        $string = str_ireplace("'", "_", $string);
        $string = str_ireplace(" ", "_", $string);
        if (is_numeric(mb_substr($string, 0, 1))) {
            if (is_numeric($string)) {
                $string = "row" . $string;
            } else {
                $string = "_" . $string;
            }
        }
        return $string;
    }

    // ****************************************************************************
}

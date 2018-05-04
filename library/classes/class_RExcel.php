<?php

class RExcel {

    private $obj;
    private $colLetters = null;

    public function __construct() {
        $this->obj = new PHPExcel();
        $this->obj->setActiveSheetIndex(0);
    }

    public function mergeCells($rowA, $colA, $rowB, $colB) {
        $this->obj->getActiveSheet()->mergeCellsByColumnAndRow($colA - 1, $rowA, $colB - 1, $rowB);
        return $this;
    }

    public function setBackgroundColor($rgbColor, $row, $col, $row2 = null, $col2 = null) {
        if ($col2 !== null) {
            $col2 = $col2 - 1;
        }
        $rgbColor = strtoupper(str_replace("#", "", $rgbColor));
        $style = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => $rgbColor)
            )
        );

        $this->obj->getActiveSheet()->getStyleByColumnAndRow($col - 1, $row, $row2, $col2)
                ->applyFromArray($style);
        return $this;
    }

    public function alignCenterHorizontal($row, $col, $row2 = null, $col2 = null) {
        if ($col2 !== null) {
            $col2 = $col2 - 1;
        }
        $this->obj->getActiveSheet()->getStyleByColumnAndRow($col - 1, $row, $row2, $col2)
                ->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        return $this;
    }

    public function alignLeftHorizontal($row, $col, $row2 = null, $col2 = null) {
        if ($col2 !== null) {
            $col2 = $col2 - 1;
        }
        $this->obj->getActiveSheet()->getStyleByColumnAndRow($col - 1, $row, $row2, $col2)
                ->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        return $this;
    }

    public function alignRightHorizontal($row, $col, $row2 = null, $col2 = null) {
        if ($col2 !== null) {
            $col2 = $col2 - 1;
        }
        $this->obj->getActiveSheet()->getStyleByColumnAndRow($col - 1, $row, $row2, $col2)
                ->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        return $this;
    }

    public function alignCenterVertical($row, $col, $row2 = null, $col2 = null) {
        if ($col2 !== null) {
            $col2 = $col2 - 1;
        }
        $this->obj->getActiveSheet()->getStyleByColumnAndRow($col - 1, $row, $row2, $col2)
                ->getAlignment()
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        return $this;
    }

    public function alignTopVertical($row, $col, $row2 = null, $col2 = null) {
        if ($col2 !== null) {
            $col2 = $col2 - 1;
        }
        $this->obj->getActiveSheet()->getStyleByColumnAndRow($col - 1, $row, $row2, $col2)
                ->getAlignment()
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        return $this;
    }

    public function alignBottomVertical($row, $col, $row2 = null, $col2 = null) {
        if ($col2 !== null) {
            $col2 = $col2 - 1;
        }
        $this->obj->getActiveSheet()->getStyleByColumnAndRow($col - 1, $row, $row2, $col2)
                ->getAlignment()
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
        return $this;
    }

    public function addNumberCol($row, $col, $value, $decimals = 2, $negativeNmbrInRed = true, $autoSizeCell = true) {
        $this->obj->getActiveSheet()->setCellValueByColumnAndRow($col - 1, $row, $value);

        $formatCode = "";
        $formatCodeDecimals = "";
        if ($decimals > 0) {
            $formatCodeDecimals = ".";
            for ($x = 0; $x < $decimals; $x++) {
                $formatCodeDecimals .= "0";
            }
        }
        if ($negativeNmbrInRed) {
            $formatCode = "#,##0" . $formatCodeDecimals . ";[red][<0]-#,##0" . $formatCodeDecimals . ";#,##0" . $formatCodeDecimals;
        } else {
            $formatCode = "#,##0" . $formatCodeDecimals . ";[<0]-#,##0" . $formatCodeDecimals . ";#,##0" . $formatCodeDecimals;
        }

        $this->obj->getActiveSheet()->getStyleByColumnAndRow($col - 1, $row)->getNumberFormat()->setFormatCode($formatCode);
        if ($autoSizeCell) {
            $this->obj->getActiveSheet()->getColumnDimensionByColumn($col - 1)->setAutoSize(true);
        }
        return $this;
    }

    public function addTextCol($row, $col, $text, $autoSizeCell = true) {
        $this->obj->getActiveSheet()->setCellValueByColumnAndRow($col - 1, $row, $text);
        if ($autoSizeCell) {
            $this->obj->getActiveSheet()->getColumnDimensionByColumn($col - 1)->setAutoSize(true);
        }
        return $this;
    }

    public function addHeaderCol($row, $col, $text, $autoSizeCell = true) {

        $styleArray = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '000000'),
                'size' => 10,
            ));

        $this->obj->getActiveSheet()->setCellValueByColumnAndRow($col - 1, $row, $text);
        $this->obj->getActiveSheet()->getStyleByColumnAndRow($col - 1, $row)->applyFromArray($styleArray);

        if ($autoSizeCell) {
            $this->obj->getActiveSheet()->getColumnDimensionByColumn($col - 1)->setAutoSize(true);
        }
        return $this;
    }

    private function makeColLetters() {
        if (!is_array($this->colLetters)) {
            $this->colLetters = array();
            $letters = range("A", "Z");
            foreach ($letters as $l) {
                $this->colLetters[] = $l;
            }
            foreach ($letters as $l) {
                foreach ($letters as $l2) {
                    $this->colLetters[] = $l . $l2;
                }
            }
        }
    }

    public function colNumberToLetter($col) {
        $this->makeColLetters();
        return isset($this->colLetters[$col - 1])?$this->colLetters[$col - 1]:"Fuck dig !!!";
    }

    public function addCalcCol($row, $col, $calc, $decimals = 2, $negativeNmbrInRed = true, $autoSizeCell = true) {
        $this->obj->getActiveSheet()->setCellValueByColumnAndRow($col - 1, $row, "=" . $calc);

        $formatCode = "";
        $formatCodeDecimals = "";
        if ($decimals > 0) {
            $formatCodeDecimals = ".";
            for ($x = 0; $x < $decimals; $x++) {
                $formatCodeDecimals .= "0";
            }
        }
        if ($negativeNmbrInRed) {
            $formatCode = "#,##0" . $formatCodeDecimals . ";[red][<0]-#,##0" . $formatCodeDecimals . ";#,##0" . $formatCodeDecimals;
        } else {
            $formatCode = "#,##0" . $formatCodeDecimals . ";[<0]-#,##0" . $formatCodeDecimals . ";#,##0" . $formatCodeDecimals;
        }

        $this->obj->getActiveSheet()->getStyleByColumnAndRow($col - 1, $row)->getNumberFormat()->setFormatCode($formatCode);
        if ($autoSizeCell) {
            $this->obj->getActiveSheet()->getColumnDimensionByColumn($col - 1)->setAutoSize(true);
        }
        return $this;
    }

    public function saveFile($path) {
        $objWriter = new PHPExcel_Writer_Excel2007($this->obj);
        $objWriter->save($path);
        return $this;
    }

}

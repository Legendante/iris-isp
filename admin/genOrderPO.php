<?php
include("db.inc.php");
include("purchaseorders.inc.php");
require_once('PHPExcel.php');
$PO_ID = createPOOrder();
$FileName = 'IRIS.' . date("Ymd") . '.PurchaseOrders.xlsx';
$styleThinBlackBorderOutline = array('borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => 'FF000000'),),),);
$styleThickBrownBorderOutline = array('borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THICK,	'color' => array('argb' => 'FF993300'),),),);
$styleThickBlackBorderOutline = array('borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THICK,	'color' => array('argb' => 'FF000000'),),),);
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("IRIS");
$objPHPExcel->getProperties()->setLastModifiedBy("IRIS");
$objPHPExcel->getProperties()->setTitle("IRIS Purchase Orders");
$objPHPExcel->getProperties()->setSubject("IRIS Purchase Orders");
$objPHPExcel->getProperties()->setDescription("Purchase Orders. Generated by IRIS");
$objPHPExcel->setActiveSheetIndex(0);
$objPHPActiveSheet = $objPHPExcel->getActiveSheet();
$objPHPActiveSheet->setTitle('Purchase Orders');
$objPHPActiveSheet->setCellValue('A1', 'PO number:');
$objPHPActiveSheet->setCellValue('B1', $PO_ID);

$ThisRow = 2;
$objPHPActiveSheet->getStyle('A' . $ThisRow . ':Q' . $ThisRow)->getFont()->setSize(10);
$objPHPActiveSheet->getStyle('A' . $ThisRow . ':Q' . $ThisRow)->getFont()->setBold(true);
$objPHPActiveSheet->getStyle('A' . $ThisRow . ':Q' . $ThisRow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPActiveSheet->getStyle('A' . $ThisRow . ':Q' . $ThisRow)->getFill()->getStartColor()->setARGB('FFCCCCCC');
$objPHPActiveSheet->getStyle('A' . $ThisRow . ':Q' . $ThisRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPActiveSheet->getStyle('A' . $ThisRow . ':Q' . $ThisRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPActiveSheet->getColumnDimension('A')->setWidth('18');
$objPHPActiveSheet->getColumnDimension('B')->setWidth('19');
$objPHPActiveSheet->getColumnDimension('C')->setWidth('14');
$objPHPActiveSheet->getColumnDimension('D')->setWidth('19');
$objPHPActiveSheet->getColumnDimension('E')->setWidth('9');
$objPHPActiveSheet->getColumnDimension('F')->setWidth('10');
$objPHPActiveSheet->getColumnDimension('G')->setWidth('21');
$objPHPActiveSheet->getColumnDimension('H')->setWidth('13');
$objPHPActiveSheet->getColumnDimension('I')->setWidth('16');
$objPHPActiveSheet->getColumnDimension('J')->setWidth('13');
$objPHPActiveSheet->getColumnDimension('K')->setWidth('13');
$objPHPActiveSheet->getColumnDimension('L')->setWidth('17');
$objPHPActiveSheet->getColumnDimension('M')->setWidth('17');
$objPHPActiveSheet->getColumnDimension('N')->setWidth('13');
$objPHPActiveSheet->getColumnDimension('O')->setWidth('20');
$objPHPActiveSheet->getColumnDimension('P')->setWidth('20');
$objPHPActiveSheet->getColumnDimension('Q')->setWidth('20');
$objPHPActiveSheet->getStyle('A' . $ThisRow)->applyFromArray($styleThinBlackBorderOutline);
$objPHPActiveSheet->getStyle('B' . $ThisRow)->applyFromArray($styleThinBlackBorderOutline);
$objPHPActiveSheet->getStyle('C' . $ThisRow)->applyFromArray($styleThinBlackBorderOutline);
$objPHPActiveSheet->getStyle('D' . $ThisRow)->applyFromArray($styleThinBlackBorderOutline);
$objPHPActiveSheet->getStyle('E' . $ThisRow)->applyFromArray($styleThinBlackBorderOutline);
$objPHPActiveSheet->getStyle('F' . $ThisRow)->applyFromArray($styleThinBlackBorderOutline);
$objPHPActiveSheet->getStyle('G' . $ThisRow)->applyFromArray($styleThinBlackBorderOutline);
$objPHPActiveSheet->getStyle('H' . $ThisRow)->applyFromArray($styleThinBlackBorderOutline);
$objPHPActiveSheet->getStyle('I' . $ThisRow)->applyFromArray($styleThinBlackBorderOutline);
$objPHPActiveSheet->getStyle('J' . $ThisRow)->applyFromArray($styleThinBlackBorderOutline);
$objPHPActiveSheet->getStyle('K' . $ThisRow)->applyFromArray($styleThinBlackBorderOutline);
$objPHPActiveSheet->getStyle('L' . $ThisRow)->applyFromArray($styleThinBlackBorderOutline);
$objPHPActiveSheet->getStyle('M' . $ThisRow)->applyFromArray($styleThinBlackBorderOutline);
$objPHPActiveSheet->getStyle('N' . $ThisRow)->applyFromArray($styleThinBlackBorderOutline);
$objPHPActiveSheet->getStyle('O' . $ThisRow)->applyFromArray($styleThinBlackBorderOutline);
$objPHPActiveSheet->getStyle('P' . $ThisRow)->applyFromArray($styleThinBlackBorderOutline);
$objPHPActiveSheet->getStyle('Q' . $ThisRow)->applyFromArray($styleThinBlackBorderOutline);
$objPHPActiveSheet->setCellValue('A' . $ThisRow, 'Reseller/ISP');
$objPHPActiveSheet->setCellValue('B' . $ThisRow, 'Cluster');
$objPHPActiveSheet->setCellValue('C' . $ThisRow, 'Project Code');
$objPHPActiveSheet->setCellValue('D' . $ThisRow, 'Customer Type');
$objPHPActiveSheet->setCellValue('E' . $ThisRow, 'Full Name');
$objPHPActiveSheet->setCellValue('F' . $ThisRow, 'House / Unit no.');
$objPHPActiveSheet->setCellValue('G' . $ThisRow, 'Complex');
$objPHPActiveSheet->setCellValue('H' . $ThisRow, 'Address');
$objPHPActiveSheet->setCellValue('I' . $ThisRow, 'Email');
$objPHPActiveSheet->setCellValue('J' . $ThisRow, 'Phone');
$objPHPActiveSheet->setCellValue('K' . $ThisRow, 'Description');
$objPHPActiveSheet->setCellValue('L' . $ThisRow, 'Package');
$objPHPActiveSheet->setCellValue('M' . $ThisRow, 'MRC');
$objPHPActiveSheet->setCellValue('N' . $ThisRow, 'ONT');
$objPHPActiveSheet->setCellValue('O' . $ThisRow, 'Contract period');
$objPHPActiveSheet->setCellValue('P' . $ThisRow, 'NRC (ONT)');
$objPHPActiveSheet->setCellValue('Q' . $ThisRow, 'Installation Cost');



?>
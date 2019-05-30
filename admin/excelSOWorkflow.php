<?php
include("db.inc.php");
$ComplexStepProgress = getAllComplexesMaxSalesOperationsSteps();
$SalesOpsSteps = getSalesOperationsWorkflow();
$Areas = getAreas();
$Suburbs = getSuburbs();
$Agents = getAgents();
$ComplextTypes = getComplexTypes();
foreach($ComplexStepProgress AS $ComplexID => $StepRec)
{
	$SOComplexes[$ComplexID] = $ComplexID;
}
$Complexes = getComplexesByIDList(implode(",", $SOComplexes));
require_once('PHPExcel.php');
$FileName = 'IRIS.' . date("Ymd") . '.SOWorkflow.xlsx';
$styleThinBlackBorderOutline = array('borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => 'FF000000'),),),);
$styleThickBrownBorderOutline = array('borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THICK,	'color' => array('argb' => 'FF993300'),),),);
$styleThickBlackBorderOutline = array('borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THICK,	'color' => array('argb' => 'FF000000'),),),);
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("IRIS");
$objPHPExcel->getProperties()->setLastModifiedBy("IRIS");
$objPHPExcel->getProperties()->setTitle("IRIS S/O Workflow");
$objPHPExcel->getProperties()->setSubject("IRIS S/O Workflow");
$objPHPExcel->getProperties()->setDescription("Sales/Operations Workflow List. Generated by IRIS");
$objPHPExcel->setActiveSheetIndex(0);
$objPHPActiveSheet = $objPHPExcel->getActiveSheet();
$objPHPActiveSheet->setTitle('SO Workflow');
$objPHPActiveSheet->mergeCells('A1:C1');
$objPHPActiveSheet->getStyle('A1')->getFont()->setSize(16);
$objPHPActiveSheet->getStyle('A1')->getFont()->setBold(true);
$objPHPActiveSheet->setCellValue('A1', 'IRIS Sales/Operations Workflow');
$ThisRow = 2;
$objPHPActiveSheet->getStyle('A' . $ThisRow . ':K' . $ThisRow)->getFont()->setSize(10);
$objPHPActiveSheet->getStyle('A' . $ThisRow . ':K' . $ThisRow)->getFont()->setBold(true);
$objPHPActiveSheet->getStyle('A' . $ThisRow . ':K' . $ThisRow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPActiveSheet->getStyle('A' . $ThisRow . ':K' . $ThisRow)->getFill()->getStartColor()->setARGB('FFCCCCCC');
$objPHPActiveSheet->getStyle('A' . $ThisRow . ':K' . $ThisRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPActiveSheet->getStyle('A' . $ThisRow . ':K' . $ThisRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPActiveSheet->getColumnDimension('A')->setWidth('18');
$objPHPActiveSheet->getColumnDimension('B')->setWidth('19');
$objPHPActiveSheet->getColumnDimension('C')->setWidth('14');
$objPHPActiveSheet->getColumnDimension('D')->setWidth('19');
$objPHPActiveSheet->getColumnDimension('E')->setWidth('16');
$objPHPActiveSheet->getColumnDimension('F')->setWidth('11');
$objPHPActiveSheet->getColumnDimension('G')->setWidth('13');
$objPHPActiveSheet->getColumnDimension('H')->setWidth('20');
$objPHPActiveSheet->getColumnDimension('I')->setWidth('16');
$objPHPActiveSheet->getColumnDimension('J')->setWidth('13');
$objPHPActiveSheet->getColumnDimension('K')->setWidth('40');
// $objPHPActiveSheet->getColumnDimension('L')->setWidth('17');
// $objPHPActiveSheet->getColumnDimension('M')->setWidth('17');
// $objPHPActiveSheet->getColumnDimension('N')->setWidth('13');
// $objPHPActiveSheet->getColumnDimension('O')->setWidth('20');
// $objPHPActiveSheet->getColumnDimension('P')->setWidth('20');
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
// $objPHPActiveSheet->getStyle('L' . $ThisRow)->applyFromArray($styleThinBlackBorderOutline);
// $objPHPActiveSheet->getStyle('M' . $ThisRow)->applyFromArray($styleThinBlackBorderOutline);
// $objPHPActiveSheet->getStyle('N' . $ThisRow)->applyFromArray($styleThinBlackBorderOutline);
// $objPHPActiveSheet->getStyle('O' . $ThisRow)->applyFromArray($styleThinBlackBorderOutline);
// $objPHPActiveSheet->getStyle('P' . $ThisRow)->applyFromArray($styleThinBlackBorderOutline);
$objPHPActiveSheet->setCellValue('A' . $ThisRow, 'Estate Name');
// $objPHPActiveSheet->setCellValue('B' . $ThisRow, 'Site Type');
$objPHPActiveSheet->setCellValue('B' . $ThisRow, 'Area');
// $objPHPActiveSheet->setCellValue('C' . $ThisRow, 'Street');
$objPHPActiveSheet->setCellValue('C' . $ThisRow, 'Units');
$objPHPActiveSheet->setCellValue('D' . $ThisRow, 'Partner');
// $objPHPActiveSheet->setCellValue('G' . $ThisRow, 'F&D Route');
// $objPHPActiveSheet->setCellValue('G' . $ThisRow, 'Responsible');
// $objPHPActiveSheet->setCellValue('H' . $ThisRow, 'Initial Intro');
$objPHPActiveSheet->setCellValue('E' . $ThisRow, 'Presentation Done');
$objPHPActiveSheet->setCellValue('F' . $ThisRow, 'MOU Signed');
$objPHPActiveSheet->setCellValue('G' . $ThisRow, 'Survey Done');
$objPHPActiveSheet->setCellValue('H' . $ThisRow, 'Body Corp Approved');
$objPHPActiveSheet->setCellValue('I' . $ThisRow, 'Bus Case Approved');
$objPHPActiveSheet->setCellValue('J' . $ThisRow, 'Developer');
$objPHPActiveSheet->setCellValue('K' . $ThisRow, 'Comments');
$ThisRow++;
foreach($Complexes AS $ComplexID => $ComplexRec)
{
	$CompUnitCount = getComplexUnitMapCount($ComplexID);
	$Workflow = getComplexSalesOperationsSteps($ComplexID);
// [stepid] => 1 [stepname] => Lead
// [stepid] => 2 [stepname] => Contact made
// [stepid] => 3 [stepname] => Bodycorp meeting
// [stepid] => 4 [stepname] => Bodycorp meeting feedback
// [stepid] => 5 [stepname] => Engagement letter returned
// [stepid] => 6 [stepname] => Site survey
// [stepid] => 7 [stepname] => Site survey feedback
// [stepid] => 8 [stepname] => Site plan returned
// [stepid] => 9 [stepname] => MOU returned
// [stepid] => 10 [stepname] => Gathering Interest
// [stepid] => 11 [stepname] => Bus. Case Approved
// [stepid] => 12 [stepname] => Build dates set
// [stepid] => 13 [stepname] => Kick Off
// [stepid] => 13 [stepname] => Kick Off

	$BCApp = ($Workflow[14] != '') ? 'Yes' : 'No';
	$BusCase = ($Workflow[11] != '') ? 'Yes' : 'No';
	$MOUSigned = ($Workflow[9] != '') ? 'Yes' : 'No';
	$SurveyDone = (($Workflow[6] != '') || ($Workflow[7] != '')) ? 'Yes' : 'No';
	$PresentDone = ($Workflow[4] != '') ? 'Yes' : 'No';
	// $IniIntro = ($Workflow[2] != '') ? 'Yes' : 'No';
	if($BusCase == 'Yes')
		$MOUSigned = 'Yes';
	// if($BusCase == 'Yes')
		// $MOUSigned = 'Yes';
	if(($MOUSigned == 'Yes') || ($SurveyDone == 'Yes'))
		$PresentDone = 'Yes';
	// if($PresentDone == 'Yes')
		// $IniIntro = 'Yes';
	$objPHPActiveSheet->setCellValue('A' . $ThisRow, $ComplexRec['complexname']);
	// $objPHPActiveSheet->setCellValue('B' . $ThisRow, $ComplextTypes[$ComplexRec['complextype']]);
	$AreaName = (isset($Suburbs[$ComplexRec['suburbid']])) ? $Suburbs[$ComplexRec['suburbid']]['suburbname'] : '-';
	$objPHPActiveSheet->setCellValue('B' . $ThisRow, $AreaName);
	// $objPHPActiveSheet->setCellValue('D' . $ThisRow, $ComplexRec['streetaddress1']);
	$objPHPActiveSheet->setCellValue('C' . $ThisRow, $CompUnitCount);
	$objPHPActiveSheet->setCellValue('D' . $ThisRow, 'Company');
	// $objPHPActiveSheet->setCellValue('G' . $ThisRow, ''); // F&D Route
	$AgentName = (isset($Agents[$ComplexRec['agentid']])) ? $Agents[$ComplexRec['agentid']]['firstname'] . " " . $Agents[$ComplexRec['agentid']]['surname'] : '-';
	// $objPHPActiveSheet->setCellValue('G' . $ThisRow, $AgentName);
	// $objPHPActiveSheet->setCellValue('H' . $ThisRow, $IniIntro);
	$objPHPActiveSheet->setCellValue('E' . $ThisRow, $PresentDone);
	$objPHPActiveSheet->setCellValue('F' . $ThisRow, $MOUSigned);
	$objPHPActiveSheet->setCellValue('G' . $ThisRow, $SurveyDone);
	$objPHPActiveSheet->setCellValue('H' . $ThisRow, $BCApp); // Body Corp Approved
	$objPHPActiveSheet->setCellValue('I' . $ThisRow, $BusCase);
	$objPHPActiveSheet->setCellValue('J' . $ThisRow, ''); // Developer
	$objPHPActiveSheet->setCellValue('K' . $ThisRow, 'Address: ' . $ComplexRec['streetaddress1']); // Comments
	$ThisRow++;
}
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $FileName . '"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
$objPHPExcel->disconnectWorksheets(); // Disconnect before you unset to properly clear the memory
unset($objPHPExcel);
unset($objWriter);
?>
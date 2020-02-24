<?php 
require('fpdf/fpdf.php');
$dbc = mysqli_connect("localhost","root","","test_");
$dbc->set_charset("utf8");

class PDF extends FPDF
{
	
	function Header()
	{
		$this->Addfont('angsa','','angsa.php');
		$this->Setfont('angsa','',14);
		$this->SetLeftMargin(10);
		$this->Cell(0,5,iconv("UTF-8","TIS-620",'ตัวอย่างการออกรายงาน'),0,1);
		$this->Cell(0,5,iconv("UTF-8","TIS-620",'กรณีศึกษาระบบจอง....'),0,1);
		$this->Cell(0,5,iconv("UTF-8","TIS-620",'ออกรายงานประจำปี พ.ศ. 2559'),0,1);
		$this->Line(5,28,200,28);
		$this->SetLeftMargin(5);
	}

	function Footer()
	{
		$this->SetLineWidth(0, 5);
		$this->Addfont('angsa','','angsa.php');
		$this->Setfont('angsa','',14);
		$this->SetY(-12);
		$this->Cell(0,5,iconv("UTF-8","TIS-620",'ทดสอบระบบ'),0,0,"L");
		$this->Cell(0,5,iconv("UTF-8","TIS-620",'เวลาพิมพ์ :'.date('d').'/'.date('m').'/'.(date('Y')+543).' '.date('H:i:s')),0,0,"R");
	}
}
function thaidate1($str)
{
	if($str == "0000-00-00"){return "ไม่กำหนด";}
	$y = substr($str, 0,4) + 543;
	$m = substr($str, 5,2) + 0;
	$d = substr($str, 8,2);
	$month = array("ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
	return $d . " " .$month[$m-1] . " " . $y;
}
function thaidate($str)
{
	if($str == "0000-00-00"){return "ไม่กำหนด";}
	$m = substr($str, 5,2) + 0;
	$month = array("มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
	return $month[$m-1];
}
$date = date('Y-m-d');
function getNumDaynews($d1,$d2)
{
	$dArr1	=preg_split("/-/", $d1);
	list($year1, $month1, $day1) = $dArr1;
	$Day1	= mktime(0,0,0,$month1,$day1,$year1);
	$dArr2	=preg_split("/-/", $d2);
	list($year2, $month2, $day2) = $dArr2;
	$Day2	= mktime(0,0,0,$month2,$day2,$year2);
	return round(abs($Day2 - $Day1) / 86400)+1;

}
$pdf = new PDF('p','mm','A4');
$pdf->Addpage();
$pdf->Ln(5);
$pdf->Addfont('angsa','','angsa.php');
$pdf->Setfont('angsa','',14);
$pdf->Cell(175,4,iconv("UTF-8","TIS-620",'สรุป'),0,1,'C');
$pdf->Ln(2);
$pdf->Cell(10,7,iconv("UTF-8","TIS-620",'ลำดับ'),1,0,'C');
$pdf->Cell(15,7,iconv("UTF-8","TIS-620",'เดือน'),1,0,'C');
$pdf->Cell(45,7,iconv("UTF-8","TIS-620",'ชื่อ'),1,0,'C');
$pdf->Cell(45,7,iconv("UTF-8","TIS-620",'จำนวน'),1,0,'C');
$pdf->Cell(18,7,iconv("UTF-8","TIS-620",'จำนวนที่'),1,0,'C');
$pdf->Cell(50,7,iconv("UTF-8","TIS-620",'วันที่'),1,0,'C');
$pdf->Cell(15,7,iconv("UTF-8","TIS-620",'ราคา'),1,0,'C');
$pdf->Ln();
$t = 0;
$total = 0;
$pdf->Addfont('angsa','','angsa.php');
$pdf->Setfont('angsa','',14);
$stmt = $dbc->prepare("SELECT MONTH(date1) as MM FROM test1 WHERE YEAR(date1) = '2016' GROUP BY MONTH(date1)");
$stmt->execute();
$stmt->bind_result($MM);
$MONTH1 = array();
while($stmt->fetch()){
	array_push($MONTH1, $MM);
}
$stmt->close();

$month = "";
$user = "";
for ($i=0; $i < count($MONTH1); $i++) { 
	$d = $MONTH1[$i];
	$stmtd = $dbc->prepare("SELECT name AS fullname,name2 AS event_title, DATE_FORMAT(date1,'%Y-%m-%d') AS date_start,DATE_FORMAT(date2,'%Y-%m-%d') AS date_end,price FROM test1 WHERE MONTH(date1) = ?");
	$stmtd->bind_param("s",$MONTH1[$i]);
	$stmtd->execute();
	$stmtd->bind_result($fullname,$event_title,$date_start,$date_end,$price);
	while ($month != $d){
		$pdf->Cell(10,7,$t+=1 ,'LR',0,'C');
		if($month != $d){
			$month = $d;
			$pdf->Cell(15,7,iconv("UTF-8","TIS-620",$date_start),'LR',0,'L');
		}else{
			$pdf->Cell(15,7,"",'LR',0,'L');
		}
		if($user != $fullname){
			$user = $fullname;
			$pdf->Cell(45,7,iconv("UTF-8","TIS-620"," ".$fullname),'LR',0,'L');
		}else{
			$pdf->Cell(45,7,iconv("UTF-8","TIS-620"," ,,"),'LR',0,'L');
		}
		$pdf->Cell(45,7,iconv("UTF-8","TIS-620",$event_title),'LR',0,'L');
		$pdf->Cell(18,7,iconv("UTF-8","TIS-620",$date_start),'LR',0,'C');
		$pdf->Cell(50,7,iconv("UTF-8","TIS-620",$date_start."ถึง".($date_end)),'LR',0,'L');
		$pdf->Cell(15,7,iconv("UTF-8","TIS-620","1000"),'LR',0,'R');
		$pdf->Ln();
		$total = $total+$price;
	}
	$stmtd->close();
	$t++;
}
$dbc->close();
$pdf->Cell(183,7,iconv("UTF-8","TIS-620",'เงิน'),1,0,'C');
$pdf->Cell(15,7,iconv("UTF-8","TIS-620",number_format($total,2)),1,0,'R');
$pdf->Output("report_po_search1.pdf","I");


?>
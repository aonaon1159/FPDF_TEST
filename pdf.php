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

function thaidate($str)
{
	if($str == "0000-00-00"){return "ไม่กำหนด";}
	$m = substr($str, 5,2) + 0;
	$month = array("มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
	return $month[$m-1];
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
$pdf->Cell(45,7,iconv("UTF-8","TIS-620",'ไปไหน'),1,0,'C');
$pdf->Cell(18,7,iconv("UTF-8","TIS-620",'จำนวนที่'),1,0,'C');
$pdf->Cell(50,7,iconv("UTF-8","TIS-620",'วันที่'),1,0,'C');
$pdf->Cell(15,7,iconv("UTF-8","TIS-620",'ราคา'),1,0,'C');
$pdf->Ln();
$t = 0;
$total = 0;
$pdf->Addfont('angsa','','angsa.php');
$pdf->Setfont('angsa','',14);

$select = "SELECT * FROM test1";
$query = mysqli_query($dbc,$select);
while (list($lumdub,$name,$event,$date1,$date2,$price) = mysqli_fetch_array($query)){


$pdf->Cell(10,7,$lumdub,'LR',0,'C');
$pdf->Cell(15,7,iconv("UTF-8","TIS-620",thaidate($date1)),'LR',0,'L');
$pdf->Cell(45,7,iconv("UTF-8","TIS-620",$name),'LR',0,'L');
$pdf->Cell(45,7,iconv("UTF-8","TIS-620",$event),'LR',0,'L');
$pdf->Cell(18,7,iconv("UTF-8","TIS-620",2),'LR',0,'C');
$pdf->Cell(50,7,iconv("UTF-8","TIS-620",$date1."ถึง".$date2),'LR',0,'L');
$pdf->Cell(15,7,iconv("UTF-8","TIS-620",$price),'LR',0,'R');
$pdf->Ln();
}
$pdf->Cell(183,7,iconv("UTF-8","TIS-620",'เงินรวม'),1,0,'C');
$pdf->Cell(15,7,iconv("UTF-8","TIS-620",1000),1,0,'R');

$pdf->Output("report.pdf","I");
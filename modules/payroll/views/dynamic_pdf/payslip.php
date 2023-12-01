<?php
require __DIR__.'../../../vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf([
    'margin_footer' => 10,
    'orientation' => 'P' // Use 'P' for portrait mode if appropriate for payslips
]);

// Set the footer for the payslip
$footer = '
<table width="100%">
    <tr>
        <td style="border:none !important;">www.zikrainfotech.com</td>
        <td style="text-align: right; border:none !important;">Page {PAGENO}</td>
    </tr>
</table>';

$mpdf->SetHTMLFooter($footer);

// Include the first page of the payslip
ob_start();
include('payslip_page1.php'); // Include the first page for the payslip
$page1 = ob_get_contents();
ob_end_clean();

$mpdf->WriteHTML($page1);

$mpdf->Output('payslip.pdf', 'I');
?>

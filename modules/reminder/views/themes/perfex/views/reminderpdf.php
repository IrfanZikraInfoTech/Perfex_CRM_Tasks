<?php 
defined('BASEPATH') or exit('No direct script access allowed');
$dimensions = $pdf->getPageDimensions();
$clientinfo = get_client($proposal->customer_id);
$supplierinfo = get_client($proposal->rel_id);
$y = $pdf->getY();
$rowcount = 1;
$pdf_logo_url = pdf_logo_url();
$pdf->writeHTMLCell(( (($dimensions['wk'] / 2) - $dimensions['rm']) ), '', '', ( $y ), $pdf_logo_url, 0, 0, false, true, ( 'J'), true);
$client_details ='<h1>'._l('job_for_quote_uppercase').'</h1><br>';
$client_details .= '<b>Sr.No.</b>'.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. $number .'<br><b>Date</b>'.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. _d($proposal->date);
$pdf->writeHTMLCell(($dimensions['wk'] / 2) - $dimensions['lm'], $rowcount *2, '', '', $client_details, 0, 1, false, true, ( 'R'), true);
$pdf->ln(15);
$y = $pdf->getY();
$proposal_info  = '<b><font  color="chocolate">' . _l('To') . '</font></b></b><br>'.$supplierinfo->company;
$proposal_info .= '<div style="color:#424242;">';
$proposal_info .= format_customerJob_info($supplierinfo , 'job', 'address');
$proposal_info .= '</div><br>';
$pdf->writeHTMLCell(($swap == '1' ? (($dimensions['wk'] ) - $dimensions['rm']) : ''), '', '', ($swap == '0' ? $y : ''), $proposal_info, 0, 0, false, true, ($swap == '0' ? 'R' : 'J'), true);
$rowcount = max([$pdf->getNumLines($proposal_info, 80)]);
$pdf->ln();
$y = $pdf->getY();
$invoice_info  = '<b><font  color="chocolate">' . _l('bill_to') . '</font></b></b>';
$invoice_info .= '<br>'.$clientinfo->company;
$invoice_info .= '<div style="color:#424242;">';
$invoice_info .= format_customerJob_info($clientinfo, 'job', 'billing');
$invoice_info .= '</div>';
$pdf->writeHTMLCell(($swap == '1' ? (($dimensions['wk'] / 2) - $dimensions['rm']) : ''), '', '', ($swap == '1' ? $y : ''), $invoice_info, 0, 0, false, true, ($swap == '0' ? 'R' : 'J'), true);
$Shipping_Address_info  = '<b><font  color="chocolate">' . _l('ship_to') . '</font></b></b>';
$Shipping_Address_info .= '<br>'.$clientinfo->company;
$Shipping_Address_info .= '<div style="color:#424242;">';
$Shipping_Address_info .= format_customerJob_info($clientinfo, 'job', 'shipping');
$Shipping_Address_info .= '</div>';
$pdf->writeHTMLCell(($dimensions['wk'] / 2) - $dimensions['lm'], $rowcount * 4, '', ($swap == '0' ? $y : ''), $Shipping_Address_info, 0, 1, false, true, ($swap == '0' ? 'J' : 'R'), true);
$pdf->ln(6);
$y = $pdf->getY();
//Model info
$model_info = '<b>Subject</b><br><br>';
$model_info .= '<b>Model</b><br><br>';
$model_info .='<b>Machine Serial No.</b>';
$pdf->writeHTMLCell(($swap == '1' ? (($dimensions['wk'] / 4) - $dimensions['rm']) : ''), '', '', ($swap == '1' ? $y : ''), $model_info, 0, 0, false, true, ($swap == '0' ? 'R' : 'J'), true);
$model_infotwo =  $proposal->subject .'<br><br>';
$model_infotwo .= $proposal->model_number.'<br><br>';
$model_infotwo .= $proposal->sr_number;
$pdf->writeHTMLCell(($dimensions['wk'] / 2) - $dimensions['lm'], $rowcount * 4, '', ($swap == '0' ? $y : ''), $model_infotwo, 0, 1, false, true, ($swap == '0' ? 'J' : 'L'), true);
$pdf->ln(4);
$items = get_items_table_data($proposal, 'proposal', 'pdf')
->set_headings('estimate');
$items_html = $items->table();
$items_html .= '<br /><br />';
$items_html .= '';
$items_html .= '<table cellpadding="6" style="font-size:' . ($font_size + 4) . 'px">';
$items_html .= '
<tr>
<td align="right" width="85%"><strong>' . _l('estimate_subtotal') . '</strong></td>
<td align="right" width="15%">' . app_format_money($proposal->subtotal, $proposal->currency_name) . '</td>
</tr>';
if (is_sale_discount_applied($proposal)) {
    $items_html .= '
    <tr>
    <td align="right" width="85%"><strong>' . _l('estimate_discount');
    if (is_sale_discount($proposal, 'percent')) {
        $items_html .= '(' . app_format_number($proposal->discount_percent, true) . '%)';
    }
    $items_html .= '</strong>';
    $items_html .= '</td>';
    $items_html .= '<td align="right" width="15%">-' . app_format_money($proposal->discount_total, $proposal->currency_name) . '</td>
    </tr>';
}
foreach ($items->taxes() as $tax) {
    $items_html .= '<tr>
    <td align="right" width="85%"><strong>' . $tax['taxname'] . ' (' . app_format_number($tax['taxrate']) . '%)' . '</strong></td>
    <td align="right" width="15%">' . app_format_money($tax['total_tax'], $proposal->currency_name) . '</td>
    </tr>';
}
if ((int) $proposal->adjustment != 0) {
    $items_html .= '<tr>
    <td align="right" width="85%"><strong>' . _l('estimate_adjustment') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($proposal->adjustment, $proposal->currency_name) . '</td>
    </tr>';
}
$items_html .= '
<tr style="background-color:#f0f0f0;">
<td align="right" width="85%"><strong>' . _l('estimate_total') . '</strong></td>
<td align="right" width="15%">' . app_format_money($proposal->total, $proposal->currency_name) . '</td>
</tr>';
$items_html .= '</table>';
if (get_option('total_to_words_enabled') == 1) {
    $items_html .= '<br /><br /><br />';
    $items_html .= '<strong style="text-align:center;">' . _l('num_word') . ': ' . $CI->numberword->convert($proposal->total, $proposal->currency_name) . '</strong>';
}
$proposal->content = str_replace('{proposal_items}', $items_html, $proposal->content);
$proposal->content = str_replace('{quote_items}', $items_html, $proposal->content);
$html = <<<EOF
<div style="width:675px !important;">
$proposal->content
</div>
EOF;
$pdf->writeHTML($html, true, false, true, false, '');

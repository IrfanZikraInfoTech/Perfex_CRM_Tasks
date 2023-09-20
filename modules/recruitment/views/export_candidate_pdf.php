<?php defined('BASEPATH') or exit('No direct script access allowed');

// Theese lines should aways at the end of the document left side. Dont indent these lines
$html = <<<EOF
<div>
$export_candidate
</div>
EOF;
$pdf->writeHTML($html, true, false, true, false, '');

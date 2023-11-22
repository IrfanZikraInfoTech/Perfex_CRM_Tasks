<style>
    body {
        font-family: 'Helvetica', 'Arial', sans-serif;
        color: #333;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10pt;
    }
    th, td {
        border: 1px solid #d3d3d3;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f8f8f8;
        color: #333;
        font-weight: normal;
        text-transform: uppercase;
    }
    .header {
        text-align: center;
        font-weight: bold;
        font-size: 14pt;
        line-height: 1.5;
    }
    .footer {
        text-align: right;
        font-size: 9pt;
    }
    .section-heading {
        background-color: #f0f0f0;
        color: #333;
        text-align: center;
        font-weight: bold;
        font-size: 11pt;
    }
    .total {
        font-weight: bold;
    }
    .total-cell {
        background-color: #f0f0f0;
        font-weight: bold;
    }
    .highlight {
        background-color: #f0f0f0;
    }
    .fixed-width {
    width: 150px; /* adjust as needed */
}

.fixed-height {
    height: 20px; /* adjust as needed */
}
.logocontainer{
    align-items:right ;
}
</style>
<!-- 
<table>
    <tr style="margin-bottom:20px" >
        <td style="border:none !important;" width="60%">
        </td>
        <td style="border:none !important;" class="logocontainer" width="40%" height="5%">
        <?php echo get_company_logo(get_admin_uri() . '/', '!tw-mt-0')?>
        </td>
    </tr>
</table> -->
<br>

<table class="">
    <tr>       
        <td colspan="4" class="header fixed-width fixed-height">

            <div style="margin-bottom:20px;">
            <?php $company_logo = get_option('company_logo'); ?>
            <img src="<?php echo base_url('uploads/company/'.$company_logo); ?>"  width="auto" height="10%"/>
            </div>
            <div>30 N Gould St, Sheridan, WY 82801, USA</div>
            <?php
                // Check if fromDate is not null and is a valid date string
                if (isset($payslip->fromDate) && $payslip->fromDate) {
                    // Use date() and strtotime() to convert the date to a month name
                    echo "<div>Payslip for the Month of " . date('F', strtotime($payslip->fromDate)) . "</div>";
                } else {
                    // Handle cases where fromDate is not set or is null
                    echo "<div>Payslip Month Not Set</div>";
                }
            ?>
        </td>
    </tr>
</table>
<br>
<table>  
    <tr>
        <td class="fixed-width fixed-height">Employee ID</td>
        <td class="fixed-width fixed-height">
            <?php
                // Fetch the custom prefix from the settings
                $custom_prefix = get_option('custom_prefix'); 

                // Concatenate the custom prefix with the staff ID from the payslip
                $employee_id_with_prefix = $custom_prefix . $payslip->staff_id;

                echo html_entity_decode($employee_id_with_prefix);
            ?>
        </td>

        <td class="fixed-width fixed-height">Employee Name</td>
        <td class="fixed-width fixed-height"><?php echo $payslip->firstname . ' '. $payslip->lastname; ?></td>
    </tr>
    <tr>
        <td class="fixed-width fixed-height">Designation</td>
        <td class="fixed-width fixed-height"><?php echo $payslip->staff_title; ?></td>
        <td class="fixed-width fixed-height">Bank Name</td>
        <td class="fixed-width fixed-height"><?php echo $payslip->bank_name; ?></td>
    </tr>
    <tr>
        <td class="fixed-width fixed-height">Department</td>
        <td class="fixed-width fixed-height"><?php echo $payslip->department_name; ?></td>
        <td class="fixed-width fixed-height">Bank A/C No</td>
        <td class="fixed-width fixed-height"><?php echo $payslip->bank_acc_no; ?></td>
    </tr>
    <tr>
        <td class="fixed-width fixed-height">DOJ</td>
        <td class="fixed-width fixed-height"><?php echo date('Y-m-d', strtotime($payslip->datecreated)); ?></td>
        <td class="fixed-width fixed-height">Account Title</td>
        <td class="fixed-width fixed-height"><?php echo $payslip->name_account; ?></td>
    </tr>
    
    <tr>
        <td colspan="4" height="10px;"></td>
    </tr>
    <tr>
        <td class="fixed-width fixed-height">Total Working Days</td>
        <td class="fixed-width fixed-height"><?php echo $payslip->totalDays; ?></td>
        <td class="fixed-width fixed-height">Paid Days</td>
        <td class="fixed-width fixed-height"><?php echo $payslip->daysPresent; ?></td>
    </tr>
     <tr>
        <td class="fixed-width fixed-height">LOP days</td>
        <td class="fixed-width fixed-height"><?php echo $payslip->unpaidleaves; ?></td>
        <td class="fixed-width fixed-height">Leaves Taken</td>
        <td class="fixed-width fixed-height"><?php echo $payslip->leaves; ?></td>
    </tr>
    <tr>
        <th colspan="2" class="section-heading ">Earnings</th>
        <th colspan="2" class="section-heading ">Deductions</th>
    </tr>
    <tr>
        <td class="fixed-width fixed-height">Basic Wage</td>
        <td class="fixed-width fixed-height"><?php echo number_format($payslip->salary, 1); ?> <?php echo $payslip->currency; ?></td>
        <td class="fixed-width fixed-height">Unpaid Leaves</td>
        <td class="fixed-width fixed-height"><?php echo number_format($payslip->unpaid_leave_deduction, 1); ?> <?php echo $payslip->currency; ?></td>
    </tr>
    <tr>
        <td class="fixed-width fixed-height">Bonus</td>
        <td class="fixed-width fixed-height"><?php echo number_format($payslip->bonus, 1); ?>  <?php echo $payslip->currency; ?></td>
        <td class="fixed-width fixed-height">Other Deductions</td>
        <td class="fixed-width fixed-height"><?php echo number_format($payslip->deduction, 1); ?>  <?php echo $payslip->currency; ?></td>
    </tr>
    <tr>
        <td class="fixed-width fixed-height">Other Allowances</td>
        <td class="fixed-width fixed-height"><?php echo number_format($payslip->allowances, 1); ?>  <?php echo $payslip->currency; ?></td>
        <td colspan="2"></td>
        
    </tr>
    <tr>
        <td class="fixed-width fixed-height">Total Earnings</td>
        <td class="fixed-width fixed-height">
             <?php 
                $totalEarnings = $payslip->salary + $payslip->bonus + $payslip->allowances;
                echo number_format($totalEarnings, 1) . ' ' . $payslip->currency;
            ?>
        </td>
        <td class="fixed-width fixed-height">Total Deductions</td>
        <td class="fixed-width fixed-height">
        <?php 
                $totalDeductions = $payslip->unpaid_leave_deduction + $payslip->deduction;
                echo number_format($totalDeductions, 1) . ' ' . $payslip->currency;
            ?>
        </td>
    </tr>
    <tr>   
        <td  class="fixed-width fixed-height">Net Salary</td>
        <td colspan="3" class="fixed-width fixed-height">
            <?php 
                $netSalary = $totalEarnings - $totalDeductions;
                echo number_format($netSalary, 1) . ' ' . $payslip->currency;
            ?>
        </td>
    </tr>
 
   
    <tr>
        <td class="fixed-width fixed-height">Amount In Words</td>
        
        <td colspan="3" class="fixed-width fixed-height">
            <?php 
                $netSalarys = $totalEarnings - $totalDeductions;
                $formatter = new NumberFormatter("en", NumberFormatter::SPELLOUT);
                $netSalaryWords = $formatter->format($netSalarys);
                echo  $netSalaryWords . ' '.'('.$payslip->currency.')' ;
            ?>
        </td>
</tr>

     
</table>
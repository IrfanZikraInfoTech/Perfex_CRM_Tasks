<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $isRTL = (is_rtl() ? 'true' : 'false'); ?>

<!DOCTYPE html>
<html lang="<?php echo $locale; ?>" dir="<?php echo ($isRTL == 'true') ? 'rtl' : 'ltr' ?>">

<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />


    <title><?php echo isset($title) ? $title : get_option('companyname'); ?></title>

    <?php echo app_compile_css(); ?>
    <?php render_admin_js_variables(); ?>

    <script>
    var totalUnreadNotifications = <?php echo $current_user->total_unread_notifications; ?>,
        proposalsTemplates = <?php echo json_encode(get_proposal_templates()); ?>,
        contractsTemplates = <?php echo json_encode(get_contract_templates()); ?>,
        billingAndShippingFields = ['billing_street', 'billing_city', 'billing_state', 'billing_zip', 'billing_country',
            'shipping_street', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country'
        ],
        isRTL = '<?php echo $isRTL; ?>',
        taskid, taskTrackingStatsData, taskAttachmentDropzone, taskCommentAttachmentDropzone, newsFeedDropzone,
        expensePreviewDropzone, taskTrackingChart, cfh_popover_templates = {},
        _table_api;
    </script>
    <style>
        .collapse{
            visibility: inherit !important;
        }
        .btn-primary{
            background-color: rgb(37 99 235/var(--tw-bg-opacity)) !important;
        }
        .btn-secondary{
            background-color: rgb(241 245 249/var(--tw-bg-opacity)) !important;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="
https://cdn.jsdelivr.net/npm/sweetalert2@11.7.28/dist/sweetalert2.all.min.js
"></script>
    <?php app_admin_head(); ?>
</head>



<body <?php echo admin_body_class(isset($bodyclass) ? $bodyclass : ''); ?>>
    <?php hooks()->do_action('after_body_start'); ?>
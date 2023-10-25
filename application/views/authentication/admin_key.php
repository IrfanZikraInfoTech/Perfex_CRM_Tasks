<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('authentication/includes/head.php'); ?>

<body class="tw-bg-neutral-100 login_admin">

    <div class="tw-max-w-md tw-mx-auto tw-pt-24 authentication-form-wrapper tw-relative tw-z-20">
        <div class="company-logo text-center">
            <?php get_dark_company_logo(); ?>
        </div>

        <h1 class="tw-text-2xl tw-text-neutral-800 text-center tw-font-semibold tw-mb-5">
            Enter Key
        </h1>

        <div class="tw-bg-white tw-mx-2 sm:tw-mx-6 tw-py-6 tw-px-6 sm:tw-px-8 tw-shadow tw-rounded-lg">
            
            <input type="password" id="key" class="tw-w-full tw-py-2 tw-px-4 tw-mb-4" />
            
            <button onclick="setKey(document.getElementById('key').value);" style="width: 100%;" class="btn btn-primary">
                Enter
            </button>
        </div>
    </div>

    <script>
        function setKey(key){
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if(xhttp.responseText == 1){
                        location.reload();
                    }else{
                        alert("Incorrect Key");
                    }
                }
            };
            xhttp.open("GET", "<?= admin_url("authentication/set_key/") ?>"+key, true);
            xhttp.send();
        }
    </script>

</body>

</html>
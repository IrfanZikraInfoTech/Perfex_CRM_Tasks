<?php
defined('BASEPATH') or exit('No direct script access allowed');
init_head();
?>

<div id="wrapper">
    <div class="content">
        <div class="col-md-12 tw-mb-5 tw-mt-5">
            <h2 class="text-center text-white mt-2 heading">Generated Images</h2>
        </div>
        <div class="container-fluid">
            <div class="row ">
                <?php if ($images): ?>
                    <?php foreach ($images as $i): ?>
                        <div class="col-md-4 tw-mt-5">
                            <div class="card">
                                <img src="<?= base_url($i->generated_image)?>" alt="Image" class="img-fluid card-img-top">
                                    <div class="card-buttons">
                                    <button class="btn-download"><i class="fa-solid fa-arrow-down" aria-hidden="true"></i></button>
                                    <button class="btn-view"><i class="fa-regular fa-eye"></i></button>   
                                    <button class="btn-delete" data-id="<?php echo $i->id; ?>"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                <div class="card-body">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No images found.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>


<?php
init_tail();
?>    


<script>
    $(document).on('click', '.btn-download', function() {
        var imgSrc = $(this).closest('.card').find('img').attr('src');
        var link = document.createElement('a');
        link.href = imgSrc;
        link.download = 'image.png';
        link.click();
    });

    $(document).on('click', '.btn-view', function() {
        var imgSrc = $(this).closest('.card').find('img').attr('src');
        window.open(imgSrc, '_blank');
    });

    $(document).on('click', '.btn-delete', function() {
        var imageId = $(this).data('id');
        var column = $(this).closest('.col-md-4');
        $.ajax({
            url: '<?php echo admin_url('AI_text/delete_image') ?>', // Replace with the actual URL to your controller method
            type: 'POST',
            data: {
                id: imageId
            },
            success: function(response) {
                column.remove();
                alert('Image deleted successfully');
            }
        });
    });

</script>

<style>
    .heading{
    font-family: "Lucida" Grande, sans-serif;
    font-size:30px;
    font-weight:bolder; 
    }
    .card {
        position:relative;
        transition: transform .2s;
        margin-bottom: 30px;

    }
    .card:hover {
        transform: scale(1.03);
    }
    .card-img-top {
        width: 100%;
        height: 250px;
        object-fit: cover;
        border-radius:20px
    }
    .card-buttons {
        margin-top: 15px;
    }
    .card-buttons .btn {
        margin-right: 5px;
    }
    .card-buttons {
        position: absolute;
        top: 75%;
        left:25%;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .card:hover .card-buttons {
        opacity: 1;
    }
    button{
        border-radius:50%;
        justify-content:center;
        border:none; 
        margin:5px;
    }
    button i {
        font-size: 20px;
        padding:5px
    }
</style>
<?php
defined('BASEPATH') or exit('No direct script access allowed');
init_head();
?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                    <h2 class="heading">AI Image Generator</h2>
            </div>
        </div>         
        <div class="container-fluid box tw-mt-5">  
                <div class="row">
                    <div class="col-md-6 tw-ml-9 tw-pt-5">
                        <div><h4 class="head4">Explain your idea.</h4></div>
                    </div>
                </div>
            <form id="imageform">    
                <div class="row">
                    <div class="col-md-11">
                        <input type="text" class="form-control" id="idea">
                        <button type="submit" class="btn btn-primary genbtn" id="genbtn">Generate</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 tw-mt-8 tw-ml-9">
                        <h5 class="head4">Advanced Settings
                        <button id="toggle-button" class="btn btn-link p-0 border-0">
                            <i id="toggle-icon" class="fa-solid fa-plus ico"></i>
                        </button>
                        </h5>
                    </div>
                </div>
                <div class="row toggle-content transition" style="display: none;">
                    <div class="col-md-3 tw-ml-9 tw-pt-5">
                        <label for="resolution text-dark" class="form-label tw-mb-3">Image Resolution:</label>
                        <select class="form-select category" id="resolution" name="resolution">
                            <option value="256x256" >256x256</option>
                            <option value="512x512" >512x512</option>
                            <option value="1024x1024">1024x1024</option>                                    
                        </select>
                    </div>
                    <div class="col-md-3 tw-pt-5">
                        <label for="style text-dark" class="form-label tw-mb-3">Art Style:</label>
                        <select class="form-select category" id="style" name="style">
                            <option value="none" >None</option>
                            <option value="pixel" >Pixel</option>
                            <option value="clay">Clay</option>
                            <option value="anime">Anime</option>
                            <option value="cartoon">Cartoon</option>    
                            <option value="Ballpoint">Ballpoint Drawing</option>     
                            <option value="Contemporary">Contemporary</option>                                    
                            <option value="Line">Line Art</option>                                    
                            <option value="Origami">Origami</option>                                    
                            <option value="Pencil">Pencil Drawing</option>                                    
                            <option value="3D">3D Render</option>                                    
                            <option value="Isometric">Isometric</option>                                    

                        </select>
                    </div>
                    <div class="col-md-3 tw-pt-5">
                        <label for="lighting text-dark" class="form-label tw-mb-3">Lighting Style:</label>
                        <select class="form-select category" id="lighting" name="lighting">
                            <option value="none" >None</option>
                            <option value="backlight" >Backlight</option>
                            <option value="warm">Warm</option>          
                            <option value="studio">Studio</option>                                    
                            <option value="neon">Neon</option>                                    
                            <option value="golden">Golden Hour</option>                                    
                            <option value="Natural">Natural</option>   
                            <option value="Studio">Studio</option>                                    
                            <option value="Foggy">Foggy</option>                                    
                            <option value="Dramatic">Dramatic</option>                                    
                                      
                        </select>
                    </div>
                    <div class="col-md-2 tw-pt-5">
                        <label for="numImages text-dark" class="form-label tw-mb-3">No. of Images:</label>
                        <select class="form-select category" id="numImages" name="numImages" min="1">
                            <option value="1" >1</option>
                            <option value="2" >2</option>
                            <option value="3">3</option>                                    
                        </select>
                </div>
                </div>
            </form>
        </div>    
        <div class="col-md-12 tw-mt-8">
            <div class="row">
                <div><h4 class="head4">Result</h4></div>            
            </div>
            <div class="row">
                <div id="imageContainer">
                    <p>No image to display</p>
                </div>
        </div> 
    </div>
</div>     

<?php
init_tail();
?>    

<script>
    $(document).ready(function() {
        $('#imageform').on('submit', function(e) {
            e.preventDefault();

            alert_float("info:", "Generating Image....")

            $.ajax({
                url: '<?php echo admin_url('AI_text/generate_image') ?>',
                type: 'POST',
                data: {
                    idea: $('#idea').val(),
                    resolution: $('#resolution').val(),
                    lighting: $('#lighting').val(),
                    style: $('#style').val(),
                    numImages: $('#numImages').val()
                },
                success: function(response) {
                    alert_float("info:", "Generated");

                    var image_urls = JSON.parse(response);
                    $('#imageContainer').empty();
                    for (var i = 0; i < image_urls.length; i++) {

                        var individualContainer = $('<div class="individual-container">');
                        var img = $('<img height="200px" width:"120px" style="margin-right:10px">');
                        img.attr('src', image_urls[i]);
                        individualContainer.append(img);

                    var btnGroup = $('<div class="btn-group">');
                    var btnView = $('<button class="btn-view"><i class="fa-regular fa-eye"></i></button>');
                    btnGroup.append( btnView);

                    individualContainer.append(btnGroup);
                    $('#imageContainer').append(individualContainer);
                    
                    }
                }
            });
        });
    });


    $(document).on('click', '.btn-view', function() {
        var imgSrc = $(this).closest('.individual-container').find('img').attr('src');
        window.open(imgSrc, '_blank');
    });

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('toggle-button').addEventListener('click', function(e) {
            e.preventDefault();

            var content = document.querySelector('.toggle-content');
            var icon = document.getElementById('toggle-icon');

            if (content.style.display === 'none') {
                content.style.display = 'block';
                icon.className = 'fa-solid fa-minus ico';
            } else {
                content.style.display = 'none';
                icon.className = 'fa-solid fa-plus ico';
            }
        });
    });


</script>



<style>
.individual-container {
    position: relative;
    display: inline-block;
}
.btn-group {
    display: none;
    position: absolute;
    bottom:0;
    justify-content:center;
    align-items:center; 

}

.btn-group  button{
    border-radius:50%;
    justify-content:center;
    border:none; 
    margin:5px;
}
.btn-group button i {
    font-size: 20px;
    padding:5px
}
.individual-container:hover .btn-group {
    display: block;
}
.ico
{   
    background-color:white;
    border-radius:50%;
    padding:10px;
    margin:5px
}
.heading {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;

}
.box{
    background-color:#BCE0FF;
    border-radius:10px;
    padding-bottom:50px;
}
#idea{
    border-radius:35px;
    margin-top:15px;
    padding:25px;
    margin-left:30px
}
.genbtn{
    position: absolute;
    right: 0;
    top: 23px;
    border-radius: 20px;
    padding:7px 20px 7px 20px;
    font-size:15px

}
.head4{
    color:black;
    font-weight:bolder;

}
.form-select {
    width: 100%;
    padding: 0.5rem;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 0.25rem;
    background-color: #fff;
    color: #333;
}

.form-select option {
    padding: 0.9rem;    
}
.category{
    border-radius:15px
}
.transition {
    transition: opacity 0.8s ease-in-out;
}

</style>
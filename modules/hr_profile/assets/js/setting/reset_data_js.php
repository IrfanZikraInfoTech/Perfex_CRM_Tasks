<script>

  function reset_data(event){
    "use strict";
    if (confirm_delete()) {
        $(event).attr( "disabled", "disabled" );
        $('#reset_data').submit(); 
    }

  }
    
</script>
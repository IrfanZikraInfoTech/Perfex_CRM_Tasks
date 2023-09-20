<script>
    
document.addEventListener('DOMContentLoaded', function() {

    //Datatable init code
    var submissionsTable = $('#submissions_table').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '<?= admin_url("recruitment_portal/get_submissions/".$id."/".$status); ?>',
            type: 'GET',
            dataType: 'json',
            dataSrc: 'submissions'
        },
        columns: [
            { 
                data: null,
                "render": function ( data, type, row ) {
                    return `
                        <a target="_blank" href="`+admin_url +`recruitment_portal/view_submission/`+ row.sub + `" class="text-blue-500 hover:text-yellow-900">`+row.name+`</button>
                    `;
                }
            },
            { data: 'campaign' },
            { 
                data: null,
                "render": function ( data, type, row ) {
                    return formatStatus(row.status, row.sub);
                }
            },
            { 
                data: 'submission_date',
            },
            { 
                data: null,
                "render": function ( data, type, row ) {
                    console.log(row);
                    return `
                    <div class="flex w-full gap-4">
                        <a target="_blank" href="`+admin_url +`recruitment_portal/view_submission/`+ row.sub + `" class="text-cyan-600 hover:text-yellow-900">View</a>
                        <a target="_blank" href="`+admin_url +`recruitment_portal/view_resume/`+ row.resume + `" class="text-lime-600 hover:text-yellow-900">Resume</a>
                    </div>
                    `;
                }
            },
            { 
                data: null,
                "render": function ( data, type, row ) {
                    var status = row.status;
                    var buttons = "";
                    var id = data.sub;
                    var name = data.name;

                    if(data.is_archived == 0){

                        buttons = formatQuickButtons(status, name, id);
                        
                    }else{
                        buttons = `
                            <div class="flex w-full gap-4">
                                <button onclick="unarchiveRecord(`+id+`)" class="text-emerald-600 hover:text-yellow-900">Unarchive</button>
                            </div>
                            `;
                    }
                    return buttons;
                }
            }

        ],
        initComplete: function() {
            $('#submissions_table_wrapper').removeClass('table-loading');
        },
        order: [[0, 'desc']],
        "createdRow": function (row, data, dataIndex) {
            var rowID = "row_" + data['sub'];
            $(row).attr('id', rowID);
        }
    });

});

function formatStatus(status, id) {
    if(status == 0) {
        return '<span id="status_'+id+'_span" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-green-800">Active</span>';
    }else if (status == 1){
        return '<span id="status_'+id+'_span" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-green-800">Rejected</span>';
    }else if (status == 2){
        return '<span id="status_'+id+'_span" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-green-800">On Hold</span>';
    }else if (status == 3){
        return '<span id="status_'+id+'_span" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-green-800">Invited</span>';
    }else if (status == 4){
        return '<span id="status_'+id+'_span" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-lime-100 text-green-800">Hired</span>';
    }
}

function formatQuickButtons(status, name, id){

    var buttons = "";


    if(status == 0){
            buttons = `
            <div class="flex w-full gap-4" id="quick_buttons_`+id+`">
                <button onclick="openActionsModal(`+id+`,'`+name+`',1)" data-toggle="modal" data-target="#templateActionModal" class="text-rose-600 hover:text-yellow-900">Reject</button>
                <button onclick="openActionsModal(`+id+`,'`+name+`',3)" data-toggle="modal" data-target="#templateActionModal" class="text-blue-600 hover:text-yellow-900">Invite</button>
                <button onclick="openActionsModal(`+id+`,'`+name+`',2)" data-toggle="modal" data-target="#templateActionModal" class="text-amber-600 hover:text-yellow-900">Hold</button>
            </div>
            `;
        } else if(status == 1){
            buttons = `
            <div class="flex w-full gap-4" id="quick_buttons_`+id+`">
                <button onclick="archiveRecord(`+id+`)" class="text-blue-600 hover:text-yellow-900">Archive</button>
                <button onclick="openActionsModal(`+id+`,'`+name+`',3)" data-toggle="modal" data-target="#templateActionModal" class="text-blue-600 hover:text-yellow-900">Invite</button>
                <button onclick="openActionsModal(`+id+`,'`+name+`',2)" data-toggle="modal" data-target="#templateActionModal" class="text-amber-600 hover:text-yellow-900">Hold</button>
            </div>
            `;
        } else if(status == 2){
            buttons = `
            <div class="flex w-full gap-4" id="quick_buttons_`+id+`">
                <button onclick="openActionsModal(`+id+`,'`+name+`',1)" data-toggle="modal" data-target="#templateActionModal" class="text-rose-600 hover:text-yellow-900">Reject</button>
                <button onclick="openActionsModal(`+id+`,'`+name+`',3)" data-toggle="modal" data-target="#templateActionModal" class="text-blue-600 hover:text-yellow-900">Invite</button>
                <button onclick="openActionsModal(`+id+`,'`+name+`',4)" data-toggle="modal" data-target="#templateActionModal" class="text-lime-600 hover:text-yellow-900">Hire</button>
            </div>
            `;
        } else if(status == 3){
            buttons = `
            <div class="flex w-full gap-4" id="quick_buttons_`+id+`">
                <button onclick="openActionsModal(`+id+`,'`+name+`',1)" data-toggle="modal" data-target="#templateActionModal" class="text-rose-600 hover:text-yellow-900">Reject</button>
                <button onclick="openActionsModal(`+id+`,'`+name+`',4)" data-toggle="modal" data-target="#templateActionModal" class="text-lime-600 hover:text-yellow-900">Hire</button>
                <button onclick="openActionsModal(`+id+`,'`+name+`',2)" data-toggle="modal" data-target="#templateActionModal" class="text-amber-600 hover:text-yellow-900">Hold</button>
            </div>
            `;
        }else if(status == 4){
            buttons = `
            <div class="flex w-full gap-4" id="quick_buttons_`+id+`">
                <button onclick="archiveRecord(`+id+`)" class="text-blue-600 hover:text-yellow-900">Archive</button>
            </div>
            `;
        }
    return buttons;
}

$(document).ready(function() {
    $('#status_select').change(function() {
        let status = $(this).val();
        let url = "<?= admin_url() ?>recruitment_portal/submissions/<?= $id ?>/" + status;
        window.location.href = url;
    });
});

</script>
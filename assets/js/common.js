/**
 * @author  Noh Seongnam 2019.01.01
 */

jQuery(document).ready(function(){



    $("#codeigniter_profiler").css("margin-left","100px;");


    $(".only_digit_str").bind("keyup",function(){
        $(this).val($(this).val().replace(/[^0-9]/g,""));
    });
	
	jQuery(document).on("click", ".deleteUser", function(){
		var userId = $(this).data("userid"),
			hitURL = baseURL + "deleteUser",
			currentRow = $(this);
		
		var confirmation = confirm("Are you sure to delete this user ?");
		
		if(confirmation)
		{
			jQuery.ajax({
			type : "POST",
			dataType : "json",
			url : hitURL,
			data : { userId : userId } 
			}).done(function(data){
				console.log(data);
				currentRow.parents('tr').remove();
				if(data.status = true) { alert("User successfully deleted"); }
				else if(data.status = false) { alert("User deletion failed"); }
				else { alert("Access denied..!"); }
			});
		}
	});



    jQuery(document).on("click", ".btn-toggle-ox", function(){
        $(this).find('i').toggleClass('fa-plus fa-minus');
        return false;
    });
    jQuery(document).on("blur", ".btn-toggle-ox", function(){
        $(this).find('i').toggleClass('fa-plus fa-minus');
        return false;
    });
	
	jQuery(document).on("click", ".close_modal_btn", function(){
		$("#pop_modal").remove();
		return false;
	});

   
	
});


//----------------------
// ajax function
// data는 is_file = false인 경우는 seialize된 데이터 (json)
// is_file = true인 경우는 FormData로 보내줄것
//----------------------
function sendAjax(url, data, callback, error_callback, async, method, data_type, is_file)
{
    $("button, .btn").prop("disabled",true);
    if(typeof is_file == 'undefined') is_file = false;
    var process_data = true;
    var content_type = 'application/x-www-form-urlencoded; charset=UTF-8';
    if(is_file){
        process_data = false;
        content_type = false;
        method = method=='GET' ? 'POST' : method; // file upload는 get 방식 불가
    }

    $.ajax({
        type: ((typeof method != 'undefined') ? method : 'POST'),
        url: url,
        data: data,
        async: (typeof async != 'undefined') ? async : false,
        processData: process_data,
        contentType: content_type,
        dataType: (typeof data_type != 'undefined') ? data_type : 'json'
    }).success(function (data, status, req) {
        if(typeof callback === "function") {
            callback(data);
        }
        $("button, .btn").prop("disabled", false);
    }).error(function(req, status, err) {
        if(typeof error_callback === "function") {
            error_callback(JSON.parse(req.responseText), req.status);
        }
        $("button, .btn").prop("disabled", false);
    });
}



$(document).ready(function(){
	$("#form").submit(function(){
		 var email = $("#email").val();
		 var name = $("#name").val();
		 var text = $("#text").val();
		 if(email != 0)
    	 {
		    if(isValidEmailAddress(email))
		    {
		    	var params =
		    	{
		    	 'name' : name,
		    	 'email' : email,
		    	 'text' : text ,
		    	};
		    	//var str = $(this).serialize();
				$.ajax({
						url : 'action/mess.php' ,
					    method : 'POST' ,
					    data : {
					        action : 'add',
					        params : params,
					    },
					    success : function(data){

					        $('#form')[0].reset();
					        alert(data);
					        pagination();
					    }
				});
			}
			else{
				alert('Заполните корректно email');
			}
		 }
		 return false;
	});
	showCom();
	pagination();
});

function showCom(page=1){
	$.ajax({
	    url : 'action/mess.php' ,
	    method : 'POST' ,
	    data : {
	        action : 'index',
	        page : page,
	    },
	    success : function(comments){
	       		$("#content").html(comments);
	       		}
	});
}

function deleteElement(id){
		$.ajax({
	    url : 'action/mess.php' ,
	    method : 'POST' ,
	    data : {
	        action : 'delete',
	        id : id,
	    },
	    success : function(comments){
	       		alert(comments);
	       		pagination();
	       		}
	});
}

function pagination(page=1){
	$.ajax({
	    url : 'action/mess.php' ,
	    method : 'POST' ,
	    data : {
	        action : 'pagination',
	        page :page,
	    },
	    success : function(comments){
	       		$(".pagination").html(comments);
	       		}
	});
	showCom(page);

}

function isValidEmailAddress(emailAddress) {
    var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
    return pattern.test(emailAddress);
}

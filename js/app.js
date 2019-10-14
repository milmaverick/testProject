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

  $('#signinBtn').on('click', function(){
  		 var admin = $("#admin").val();
  		 var passwd = $("#password1").val();
  		 if(admin != 0 && passwd != 0)
      	 {
  		    	var params =
  		    	{
  		    	 'admin' : admin,
  		    	 'passwd' : passwd ,
  		    	};
  				$.ajax({
  						url : 'action/mess.php' ,
  					  method : 'POST' ,
  					  data : {
  					        action : 'signin',
  					        params : params,
  					    },
              success : function(data){
                    showCom();
  					    }
  				});
  		 }
       else{
         alert('Заполните логин и пароль');
       }
  		 return false;
  	});

    $('#logOut').on('click', function(){
      $.ajax({
          url : 'action/mess.php' ,
            method : 'POST' ,
            data : {
                action : 'logOut',
            },
          success : function(data){
                  showCom();
              }
      });
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
          },
	});
	showCom(page);
}

function isValidEmailAddress(emailAddress) {
    var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
    return pattern.test(emailAddress);
}

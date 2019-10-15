$(document).ready(function(){

	pagination();
	isLogged();

	$("#form").submit(function(){
		 var email = HtmlEncode($("#email").val());
		 var name =  HtmlEncode($("#name").val());
		 var text =  HtmlEncode($("#text").val());
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
					        pagination();
					    },
							error : function(data){
								alert("ошибка");
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
										pagination();
										$("#logOut").show();
										$("#login").hide();
										$('.res a').show();
										$('#signinBtn').hide();
  					    },

							error : function(data){
									alert("ошибка");
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
									pagination();
                  $('.res a').hide();
									$("#logOut").hide();
									$("#login").show();
									$('#signinBtn').show();
              },
					error : function(data){
								alert("ошибка");
							}
      });
    });

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
	       		},
			error : function(comments){
							alert("ошибка");
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
	       		alert('Удалено');
	       		pagination();
	       		},
			error : function(comments){
							alert("ошибка");
						}
	});
}

function changeElement(id){
	$.ajax({
		url : 'action/mess.php' ,
		method : 'POST' ,
		data : {
				action : 'change',
				id : id,
		},
		success : function(comments){
					alert('изменено');
					$('#comment'+ id).hide();
					pagination();
					},
		error : function(comments){
						alert("ошибка");
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
							// alert(comments);
	       		$(".pagination").html(comments);
					//	isLogged();
          },
			error : function(comments){
						alert("ошибка");
					}
	});
		showCom(page);
}

function isLogged (){
	$.ajax({
			url : 'action/mess.php' ,
				method : 'POST' ,
				data : {
						action : 'isLogged',
				},
	}).done(function( msg ) {
		if(msg=="true"){
			$("#logOut").show();
			$("#login").hide();
			$('.res a').show();
		}
		else{
			$("#logOut").hide();
			$("#login").show();
			$('.res a').hide();
		}
	})
}

function isValidEmailAddress(emailAddress) {
    var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
    return pattern.test(emailAddress);
}

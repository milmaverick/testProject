$(document).ready(async function(){
	await getPage();
	//await pagination(getPage());
	await isLogged();

 //Отправка комментария--------------------

	$("#form").submit(function(){
		 var elem = this;
		 var formData = new FormData(this);
		 var email = formData.get('email');
		 var name =  formData.get('name');
		 var text =  formData.get('text');

		 if(formData.get('name')){

			 if(formData.get('uploadimage')){
				 if(!formData.get('uploadimage').type.match(/(.png)|(.jpeg)|(.jpg)|(.gif)$/i))  {
 					 alert('НЕ тот формат. Картинка дожна быть : JPG, GIF, PNG');
 					 return false;
 				 }
			 }

			if(email != 0)
			     {
						    if(isValidEmailAddress(email))
						    {
									$.ajax({
							          type: "POST",
							          url: "action/upload.php",
							          data:  formData,
							          processData: false,
							          contentType: false,
							          success: function(data)
												{
													if(data=='true')
													{
														 $('#form')[0].reset();
														 $('#alert-success-msg').show();
														 setTimeout(function(){
	 												   $('#alert-success-msg').hide();
	 												 }, 5000);
													}
													else{
														alert(data);
													}
							          }
							     });
							}
							else{
								alert('Заполните корректно email');
							}
					 }
		 }
		 return false;
	});

	$('select').on('change', function() {
		showCom();
	});

	//Вход В админку---------------------------------------

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
  						url : 'action/admin.php' ,
  					  method : 'POST' ,
  					  data : {
  					        action : 'signin',
  					        params : params,
  					    },
  				}).done(function( msg ) {
							if(msg=="true") {
								//pagination();
								getPage();

								$('#alert-success').html('Успешный вход!');
								$("#alert-success").show();
								setTimeout(function(){
							    $('#alert-success').hide();
							 }, 5000);
								$("#logOut").show();
								$("#login").hide();
								$('.res a').show();
								$('#signinBtn').hide();
							}
							else{
								$('#alert-danger').html(msg);
								$("#alert-danger").show();
								setTimeout(function(){
							    $('#alert-danger').hide();
							 }, 5000);
							}
					});
  		 }
       else{
         alert('Заполните логин и пароль');
       }
  		 return false;
  	});

		//Выход из админки--------------

	  $('#logOut').on('click', function(){
	      $.ajax({
	          url : 'action/admin.php' ,
	            method : 'POST' ,
	            data : {
	                action : 'logOut',
	            },
	          success : function(data){
										//pagination();
										getPage();
										$('.alert').hide();
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

		//Измениние комментария--------------------------------

		$('#updateCom').on('click', function(){
				 var id = $("#idChange").val();
	  		 var name = $("#nameChange").val();
	  		 var email = $("#emailChange").val();
				 var text = $("#textChange").val();
	  		 if(name != 0 && email != 0 && text !=0 )
	      	 {
	  		    	var params =
	  		    	{
								'id' : id,
	  		    	 'name' : name,
	  		    	 'email' : email ,
							 'text' : text ,
	  		    	};
	  				$.ajax({
	  						url : 'action/admin.php' ,
	  					  method : 'POST' ,
	  					  data : {
	  					        action : 'update',
	  					        params : params,
	  					    },
	  				}).done(function( msg ) {
								if(msg=="true") {
									getPage();
									//pagination();
									$('#alert-success').html('Комментарий Изменен');
									$("#alert-success").show();
									setTimeout(function(){
								    $('#alert-success').hide();
								 }, 5000);

								}
								else{
									$('#alert-danger').html(msg);
									$("#alert-danger").show();
									setTimeout(function(){
								    $('#alert-danger').hide();
								 }, 5000);
								}
						});
	  		 }
	       else{
	         alert('Заполните все поля');
	       }
	  		 return false;
	  	});

		//Форма для Входа в админку-------------------------

		$('#login').on('click', ()=> {
						$('#form2').show();
					  $('#signInFormButton').show();
						$('#formChange').hide();
					  $('#formChangeButton').hide();
					});
});

//вывести все комментарии--------------------------------

function showCom(page=1){
	var sort = $('select').val();
	console.log('sdsd '+sort);
	$.ajax({
	    url : 'action/mess.php' ,
	    method : 'POST' ,
	    data : {
	        action : 'index',
	        page : page,
					sort : sort,
	    },
	    success : function(comments){
	       		$("#content").html(comments);
	       		},
			error : function(){
							alert("ошибка");
						}
	});
}

//Удалить комментарий ------------------------------------------------

function deleteElement(id){
		$.ajax({
	    url : 'action/admin.php' ,
	    method : 'POST' ,
	    data : {
	        action : 'delete',
	        id : id,
	    },
	    success : function(comments){
					if (comments=="true") {
						alert('Удалено');
						$('#return'+id).removeClass('displayNone');
						$('#delete'+id).hide();
						$('#delete'+id).parent().parent()
						.parent()
						.addClass('deleteElement');
					}
					else {
						alert(comments);
					}
	   	},
			error : function(comments){
							alert("ошибка");
						}
	});
}

//Форма для изменения комментарий ------------------------------------------------

function changeElement(id){
	$('#formChange').show();
  $('#formChangeButton').show();
	$('#form2').hide();
  $('#signInFormButton').hide();
	$.ajax({
			url : 'action/mess.php' ,
			method : 'POST' ,
			data : {
					action : 'change',
					id : id,
			},
			success : function(comments){
				comments=JSON.parse(comments);
				$('#idChange').html(comments[0]['id']);
				$('#nameChange').html(comments[0]['name']);
				$('#emailChange').html(comments[0]['email']);
				$('#textChange').html(comments[0]['text']);
						},
			error : function(comments){
							alert("ошибка");
						}
		});
}

//Пропустить комментарий ------------------------------------------------

function accessElement(id){
	$.ajax({
		url : 'action/admin.php' ,
		method : 'POST' ,
		data : {
				action : 'access',
				id : id,
		},
		success : function(comments){
			$('#comment'+id).removeClass('deleteElement');
			$('#return'+id).hide();
			$('#delete'+id).show();

		},
		error : function(comments){
						alert("ошибка");
		}
	});
}

//Пагинация страниц комментарий ------------------------------------------------------

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

function getPage() {
	$.ajax({
	    url : 'action/mess.php' ,
	    method : 'POST' ,
	    data : {
	        action : 'getPage',
	    },
	    success : function(comments){
	       		comments=JSON.parse(comments);
						pagination(comments);
						console.log(comments);
          },
			error : function(comments){
						alert("ошибка");
					}
	});
}

//Влидация email ---------------------------------------------------------

function isValidEmailAddress(emailAddress) {
    var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
    return pattern.test(emailAddress);
}

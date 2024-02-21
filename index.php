<!DOCTYPE html>
<?php

require_once ($_SERVER["DOCUMENT_ROOT"] . "/test/classes/CurlHandler.php");
$service = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/test/settings/service.json"));

$CurlHandler = new CurlHandler();
$responce = $CurlHandler->exec("https://dev.expocar.ru/test", 'GET', array(), array(
	'Content-Type: application/json', 
	"Authorization: Bearer {$service->token}"
));
$responce->phone = substr($responce->phone, 1);

?>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Откликнуться</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		<link href="style.css" rel="stylesheet">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

	</head>
	<body class="bg-light">
		<div class="container">
			<div class="py-5 text-center">
				<h2>Откликнуться</h2>
			</div>

			<div class="row">
				<div class="col-12 col-md-10 col-lg-6 mx-auto">
					<form class="needs-validation" novalidate>
						<div class="alert alert-success" role="alert" style="display: none;">
							This is a success alert—check it out!
						</div>

						<div class="mb-3">
							<label for="name">Ф.И.О. <span style="color: red;">*</span></label>
							<input type="text" class="form-control" id="name" value="<?=$responce->name?>" placeholder="Например, Иванов Иван Иванович" required>
							<div class="invalid-feedback" style="width: 100%;">
								Поле "Ф.И.О." обязательно для заполнения
							</div>
						</div>
						<div class="mb-3">
							<label for="phone">Телефон <span style="color: red;">*</span></label>
							<input type="text" class="form-control" id="phone" value="<?=$responce->phone?>" placeholder="Например, +7 (951) 234-234-23" required>
							<div class="invalid-feedback feedback-pos" style="width: 100%;">
								Поле "Телефон" обязательно для заполнения
							</div>
						</div>

						<div class="mb-3">
							<label for="email">E-mail <span style="color: red;">*</span></label>
							<input type="email" name="email" value="<?=$responce->email?>" placeholder="Например, test@mail.ru" class="form-control" id="email" required>
							<div class="invalid-feedback feedback-pos">
								Поле "E-mail" заполнено не корректно
							</div>
						</div>
						<hr class="mb-4">
						<div class="mb-3">
							<button class="btn btn-primary btn-lg btn-block submit-form" type="submit">Отправить</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
		<script src="https://rawgit.com/RobinHerbots/Inputmask/5.x/dist/jquery.inputmask.js"></script>
		<script type="text/javascript">
			const $ = jQuery.noConflict()

			$(document).ready(() => { 
				$('#phone').inputmask({"mask": "+7 (999) 999-99-99"})
			})

			function sendForm () {  

				$(".submit-form")
				.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Отправка...')
				.prop("disabled", true)
				$("#name, #phone, #email").prop("disabled", true)

				$.ajax({
					url: `${window.location.origin}/test/ajax/send_form.php`,
					method: 'POST',
					dataType: 'json',
					data: {
						'name': $("#name").val(),
						'phone': $("#phone").val(),
						'email': $("#email").val()
					}, 
					success: function(data){   
						// console.log(JSON.parse(data));
						// const json_data = JSON.parse(data)
						console.log(data)
						$(".submit-form")
						.html('Отправить')
						.prop("disabled", false)
						$("#name, #phone, #email").prop("disabled", false)

						if (!$(".alert").hasClass("alert-success")) $(".alert").addClass("alert-success")
						if (data.err_code === 2) $(".alert").removeClass("alert-success").addClass("alert-danger")

						$(".alert").html(data.message).fadeIn()
						setTimeout(() => {
							$(".alert").fadeOut()
						}, 2000);
					}
				})

			}

			(function() {
				'use strict';
				window.addEventListener('load', function() {
					var forms = document.getElementsByClassName('needs-validation')
					var validation = Array.prototype.filter.call(forms, function(form) {
						form.addEventListener('submit', function(event) {
							if (form.checkValidity() === false) {
								event.preventDefault()
								event.stopPropagation()
							} else {
								event.preventDefault()
								sendForm()
							}	
							form.classList.add('was-validated')
						}, false)
					})
				}, false)
			})()
		</script>
	</body>
</html>
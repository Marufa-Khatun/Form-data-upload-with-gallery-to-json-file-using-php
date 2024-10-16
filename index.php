<?php


if (file_exists(__DIR__ . "/templates/autoload.php")) {
	require_once __DIR__ . "/templates/autoload.php";
} else {
	echo "The required file does not exist.";
}



if ($_SERVER['REQUEST_METHOD'] == "POST") {
	//get form value
	$name = trim(htmlentities(($_POST['name'])));
	$phone = trim(htmlentities(($_POST['phone'])));
	$email = trim(htmlentities(($_POST['email'])));
	$gender = isset($_POST['gender']) ? $_POST['gender'] : [];
	$skills = isset($_POST['skills']) ? $_POST['skills'] : [];

	//upload single photo
	$tmp_name = $_FILES['photo']['tmp_name'];
	$fileName = $_FILES['photo']['name'];
	$fileSize = $_FILES['photo']['size'] / 1024;
	//create unique name 
	$fileArray = explode('.', $fileName);
	$getExtention = strtolower(end($fileArray));
	$uniqueFileName = time() . '_' . rand(1000000, 10000000) . '.' . $getExtention;

	//Validations
	$error = [];

	//Name Field Validation
	if (empty($name)) {
		$msg = createAlert('All Filed are Required!');
		$error['name'] = 'Name Feild is Required!';
	}

	//Phone Field Validation
	if (empty($phone)) {
		$msg = createAlert('All Filed are Required!');
		$error['phone'] = 'Phone Feild is Required!';
	}

	$pattern = "/^(\+88)?0 ?1[2-9]{1}\d{8}$/";
	if (!preg_match($pattern, $phone)) {
		$error['phone'] = 'Enter Valid Phone Number!';
	}

	//Email Field Validation
	if (empty($email)) {
		$msg = createAlert('All Filed are Required!');
		$error['email'] = 'Email Feild is Required!';
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error['email'] = 'Enter Your Valid Email!';
	}

	//Gender Field Validation

	if (empty($gender)) {
		$msg = createAlert('All Filed are Required!');
		$error['gender'] = 'Gender Feild is Required!';
	}
	//Skills Field Validation

	if (empty($skills)) {
		$msg = createAlert('All Filed are Required!');
		$error['skills'] = 'Skills Feild is Required!';
	}

	//Profile Field Validation
	if (!in_array($getExtention, ['png', 'jpg', 'jpeg', 'gif', 'webp'])) {
		$msg = createAlert('All Filed are Required!');
		$error['photo'] = 'Profile Feild is Required!';
	} elseif ($fileSize >= 3000) {
		$msg = createAlert('Invalid file size', 'warning');
		$error['photo'] = 'File toolarge!';
	}

	if (empty($error)) {

		move_uploaded_file($tmp_name, 'photos/' . $uniqueFileName);


		//upload galley photos
		for ($i = 0; $i < count($_FILES['gallery']['name']); $i++) {
			$gall_name = $_FILES['gallery']['name'][$i];
			$gall_tmp_name = $_FILES['gallery']['tmp_name'][$i];

			$GallFileArray = explode('.', $fileName);
			$getGallExtention = strtolower(end($GallFileArray));
			$uniqueGallFileName = time() . '_' . rand(1000000, 10000000) . '.' .  $getGallExtention;
			move_uploaded_file($gall_tmp_name, 'gallery/' .  $uniqueGallFileName);
		}


		$data = json_decode(file_get_contents('db/devs.json'), true);
		array_push($data, [
			"name" => $name,
			"phone" => $phone,
			"email" => $email,
			"gender" => $gender,
			"skills" => $skills,
			"photo" => $uniqueFileName,
			"gallery" => $uniqueGallFileName,
		]);
		file_put_contents('db/devs.json', json_encode($data));

		$msg = createAlert('Data Created!', 'success');
		resetForm();

		$error = [];
	}
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="assets/css/style.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<title>Profile Upload </title>

</head>

<body>

	<div class="container">
		<div class="row my-5 justify-content-center">
			<div class="col-md-5">
				<div class="card">
					<div class="card-header">
						<h1>Profile upload</h1>
					</div>
					<div class="card-body">

						<form action="" method="POST" enctype="multipart/form-data">
							<?php echo $msg ?? '' ?>
							<div class="my-3">
								<label for="">Name </label>
								<input type="text" name="name" value="<?php echo oldValue('name') ?>" class="form-control">
								<p class="text-danger"><?php echo $error['name'] ?? '' ?></p>
							</div>
							<div class="my-3">
								<label for="">Phone </label>
								<input type="text" name="phone" value="<?php echo oldValue('phone') ?>" class="form-control">
								<p class="text-danger"><?php echo $error['phone'] ?? '' ?></p>
							</div>
							<div class="my-3">
								<label for="">Email </label>
								<input type="text" name="email" value="<?php echo oldValue('email') ?>" class="form-control">
								<p class="text-danger"> <?php echo $error['email'] ?? '' ?></p>
							</div>


							<div class="my-3">
								<label for="">Gender </label>
								<input type="radio" name="gender" value="Male" <?php echo (oldValue('gender') == 'Male') ? 'checked' :  '' ?>>Male
								<input type="radio" name="gender" value="Female" <?php echo (oldValue('gender') == 'Female') ? 'checked' :  '' ?>> Female
								<input type="radio" name="gender" value="others" <?php echo (oldValue('gender') == 'others') ? 'checked' : '' ?>>others
								<p class="text-danger"><?php echo $error['gender'] ?? '' ?></p>
							</div>


							<div class="my-3">
								<label for="">Skills </label>
								<input type="checkbox" name="skills[]" value="PHP" <?php echo (in_array('PHP', oldValue('skills', []))) ? 'checked' : '' ?>>PHP
								<input type="checkbox" name="skills[]" value="Laravel" <?php echo (in_array('Laravel', oldValue('skills', []))) ? 'checked' : '' ?>>Laravel
								<input type="checkbox" name="skills[]" value="Js" <?php echo (in_array('Js', oldValue('skills', []))) ? 'checked' : '' ?>>JS
								<input type="checkbox" name="skills[]" value="React" <?php echo (in_array('React', oldValue('skills', []))) ? 'checked' : '' ?>>React
								<p class="text-danger"><?php echo $error['skills'] ?? '' ?></p>
							</div>
							<div class="my-3">
								<label class="image">
									<label for="">Profile Photo </label>
									<input type="file" id="profile-photo" name="photo" class="form-control">
									<img id="profile-photo-icon" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQrVLGzO55RQXipmjnUPh09YUtP-BW3ZTUeAA&s" alt="">
									<p class="text-danger"><?php echo $error['photo'] ?? '' ?></p>
								</label>


								<div class="preview-image">
									<img id="profile-photo-preview" src="">
									<button type="button" id="profile-photo-close"><i class="fa fa-times"></i></button>
								</div>
							</div>
							<div class="my-3">
								<label class="image">
									<label for="">Gallery Photos </label>
									<input type="file" multiple id="gallery-photos" name="gallery[]" class="form-control">
									<img id="gallery-photo-icon" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQrVLGzO55RQXipmjnUPh09YUtP-BW3ZTUeAA&s" alt="">
								</label>
								<div class="preview-gallery">


								</div>
							</div>
					</div>


					<div class="my-3">
						<input type="submit" value="Submit" class="form-control bg-primary">
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

	<script>
		//profile photo preview
		const profilePhoto = document.getElementById("profile-photo");
		const profilePreview = document.getElementById("profile-photo-preview");
		const profileIcon = document.getElementById("profile-photo-icon");
		const profileClose = document.getElementById("profile-photo-close");

		profilePhoto.onchange = (event) => {
			imageUrl = URL.createObjectURL(event.target.files[0]);
			profilePreview.setAttribute('src', imageUrl);
			profileIcon.style.display = 'none';
			profileClose.style.display = 'block';

		}

		profileClose.onclick = () => {
			profilePreview.setAttribute('src', "");
			profileIcon.style.display = 'block';
			profileClose.style.display = 'none';

		}

		//gallery photo preview

		const galleryPhotos = document.getElementById('gallery-photos');
		const previewGallery = document.querySelector('.preview-gallery');
		const galleryPhotoIcon = document.getElementById('gallery-photo-icon');
		const galleryPhotoClose = document.getElementById('gallery-photo-close');


		let preItem = '';
		galleryPhotos.onchange = (event) => {
			for (let j = 0; j < event.target.files.length; j++) {
				const gallaryURL = URL.createObjectURL(event.target.files[j]);
				preItem += `<div class="gallery-item">
                                        <img id="gallery-photo-preview" src="${gallaryURL}">
                                        <button type="button" class="gallery-photo-close"><i class="fa fa-times"></i></button>
                                    </div>`;
			}
			previewGallery.innerHTML = preItem;
			galleryPhotoIcon.style.display = 'none';

			// Now, select all close buttons
			const closeButtons = document.querySelectorAll('.gallery-photo-close');

			// Loop through each close button
			closeButtons.forEach((button) => {
				button.onclick = () => {
					button.closest('.gallery-item').remove(); // Remove the clicked gallery item
					if (previewGallery.children.length === 0) {
						galleryPhotoIcon.style.display = 'block'; // Show the icon again if no images left
					}
				};

			});


		}

		// When gallery icon is clicked again
		galleryPhotoIcon.onclick = () => {

			galleryPhotos.onchange = (event) => {
				preItem = ''; // Reset preItem
				for (let j = 0; j < event.target.files.length; j++) {
					const galleryURL = URL.createObjectURL(event.target.files[j]);
					preItem += `<div class="gallery-item">
                            <img id="gallery-photo-preview" src="${galleryURL}">
                            <button type="button" class="gallery-photo-close"><i class="fa fa-times"></i></button>
                        </div>`;
				}
				previewGallery.innerHTML = preItem;
				galleryPhotoIcon.style.display = 'none'; // Hide the icon when photos are selected again

				// Attach event listeners to new close buttons
				const closeButtons = document.querySelectorAll('.gallery-photo-close');
				closeButtons.forEach((button) => {
					button.onclick = () => {
						button.closest('.gallery-item').remove(); // Remove the gallery item
						if (previewGallery.children.length === 0) {
							galleryPhotoIcon.style.display = 'block'; // Show the icon again if no images left
						}
					};
				});
			}
		}
	</script>

</body>

</html>
<h1>Edit User</h1>
<form method='post' action='#'>
    <div class="form-group">
        <label for="first_name">First Name</label>
        <input type="text" class="form-control" id="first_name" placeholder="Enter First Name" name="first_name" value ="<?php if (isset($user["first_name"])) echo $user["first_name"];?>" required>
    </div>
    <div class="form-group">
        <label for="last_name">Last Name</label>
        <input type="text" class="form-control" id="last_name" placeholder="Enter First Name" name="last_name" value ="<?php if (isset($user["last_name"])) echo $user["last_name"];?>" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="text" class="form-control" id="email" placeholder="Enter Email" name="email" value ="<?php if (isset($user["email"])) echo $user["email"];?>" required>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control" id="password" placeholder="Enter Password" name="password" required>
    </div>
    <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" class="form-control" id="confirm_password" placeholder="Confirm Password" name="confirm_password" required>
    </div>
    <div class="form-group">
        <label for="type">Type</label>
        <select name="type" id="type" class="form-control">
		  <option value="1" <?php if($user["type"] == 1): ?> selected="selected" <?php endif; ?> >Admin</option>
		  <option value="2" <?php if($user["type"] == 2): ?> selected="selected" <?php endif; ?>>Employee</option>
		</select>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
</form>
<script type="text/javascript">
	var password = document.getElementById("password")
	  , confirm_password = document.getElementById("confirm_password");

	function validatePassword(){
	  if(password.value != confirm_password.value) {
	    confirm_password.setCustomValidity("Passwords Don't Match");
	  } else {
	    confirm_password.setCustomValidity('');
	  }
	}

	password.onchange = validatePassword;
	confirm_password.onkeyup = validatePassword;
</script>
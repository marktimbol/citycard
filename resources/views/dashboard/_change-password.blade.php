<h2>Change Password</h2>
<form method="POST">
	{{ csrf_field() }}
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
				<label for="old_password">Old Password</label>
				<input type="password"
					id="old_password"
					name="old_password"
					class="form-control" />
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label for="password">New Password</label>
				<input type="password"
					id="password"
					name="password"
					class="form-control" />
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<label for="password_confirmation">Repeat New Password</label>
				<input type="password"
					id="password_confirmation"
					name="password_confirmation"
					class="form-control" />
			</div>
		</div>
	</div>

	<div class="form-group">
		<button type="submit" class="btn btn-primary">Change Password</button>
	</div>
</form>
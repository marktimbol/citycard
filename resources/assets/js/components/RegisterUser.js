import React from 'react';

class RegisterUser extends React.Component
{
	constructor(props)
	{
		super(props);

		this.state = {
			email: '',
			name: '',
			password: '',
			password_confirmation: '',

			isSubmitting: false,
			buttonText: 'Sign Up',

			errors: [],
		}

		this.handleChange = this.handleChange.bind(this);
		this.onRegister = this.onRegister.bind(this);
	}

	handleChange(e) {
		this.setState({
			[e.target.name]: e.target.value
		});
	}	

	onRegister(e)
	{
		e.preventDefault();

		this.isSubmitting();
		
		let that = this;

		$.ajax({
		    url: '/register',
		    type: 'POST',
		    dataType: 'json',
		    data: $('#RegisterUserForm').serialize(),
		    headers: { 'X-CSRF-Token': App.csrfToken },
		    success: function(response) {
		    	that.setState({
		    		isSubmitting: false,
		    		buttonText: 'Sign Up'
		    	})

		        swal({
		            title: "City Card",
		            text: "Welcome to City Card",
		            type: "success",
		            showConfirmButton: true
		        }, function() {
					window.location = '/posts';
				});
				
		    },
		    error: function(response) {
		    	console.log(response);

		    	that.setState({
		    		isSubmitting: false,
		    		buttonText: 'Sign Up',
		    		errors: response.responseJSON,
		    	})
		    }
		})
	}

	isSubmitting() {
		this.setState({
			isSubmitting: true,
			buttonText: 'Signing up'
		})
	}

	render()
	{
		let errors = this.state.errors;
		let emailClass = errors.hasOwnProperty('email') ? 'form-group has-error' : 'form-group';
		let nameClass = errors.hasOwnProperty('name') ? 'form-group has-error' : 'form-group';
		let passwordClass = errors.hasOwnProperty('password') ? 'form-group has-error' : 'form-group';
		let confirmationClass = errors.hasOwnProperty('password_confirmation') ? 'form-group has-error' : 'form-group';

		return (
			<div>
				<div className="Box Registration">
					<h1 className="text-center">City Card</h1>
					<p className="lead text-center">
						Sign up to see news, deals, and events from places around you.
					</p>
					<div className="Registration__form">
						<div className="Social__login">
							<a href="/auth/facebook" className="btn btn-block btn-primary">
								<i className="fa fa-facebook-official"></i> 
								Login with Facebook
							</a>

							<a href="/auth/google" className="btn btn-block btn-danger">
								<i className="fa fa-google-plus"></i> 
								Login with Google
							</a>																
						</div>

						<div className="or--divider">
							<div className="or__line"></div>
								<p className="or__text">or</p>
							<div className="or__line"></div>
						</div>

						<form method="POST" id="RegisterUserForm" onSubmit={this.onRegister}>
							<div className={emailClass}>
								<input type="text" 
									name="email" 
									className="form-control" 
									value={this.state.email}
									onChange={this.handleChange}
									placeholder="Email Address" />
									{ errors.hasOwnProperty('email') ?
										<span className="help-block">{ errors['email'] }</span>
										: <span></span>
									}										
							</div>
							<div className={nameClass}>
								<input type="text" 
									name="name" 
									className="form-control"
									value={this.state.name}
									onChange={this.handleChange}
									placeholder="Full Name" />
									{ errors.hasOwnProperty('name') ?
										<span className="help-block">{ errors['name'] }</span>
										: <span></span>
									}									
							</div>	
							<div className={passwordClass}>
								<input type="password" 
									name="password" 
									className="form-control" 
									value={this.state.password}
									onChange={this.handleChange}
									placeholder="Password" />
									{ errors.hasOwnProperty('password') ?
										<span className="help-block">{ errors['password'] }</span>
										: <span></span>
									}									
							</div>	
							<div className={confirmationClass}>
								<input type="password" 
									name="password_confirmation" 
									className="form-control"
									value={this.state.password_confirmation}
									onChange={this.handleChange}
									placeholder="Repeat Password" />
									{ errors.hasOwnProperty('password_confirmation') ?
										<span className="help-block">{ errors['password_confirmation'] }</span>
										: <span></span>
									}									
							</div>	
							<div className="form-group">
								<button type="submit" 
									className="btn btn-block btn-primary"
									onClick={this.onRegister}
									disabled={this.state.isSubmitting}
								>
									{ this.state.isSubmitting ?
										<span>&nbsp; <i className="fa fa-spinner fa-spin"></i></span> :
										<span></span>
									}
									{this.state.buttonText}
								</button>
							</div>
							<p className="Accept-terms text-center">
								By signing up, you agree to our<br />
								<a href=''>Terms</a> &amp; <a href="#">Privacy Policy</a>.
							</p>
						</form>
					</div>
				</div>
				<div className="Box Login">
					<p>Have an account? <a href="#" onClick={this.props.showLogin}>Log in</a></p>
				</div>				
			</div>
		)
	}
}

export default RegisterUser;
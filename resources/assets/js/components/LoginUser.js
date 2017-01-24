import React from 'react';

class LoginUser extends React.Component
{
	constructor(props)
	{
		super(props);

		this.state = {
			email: '',
			password: '',

			isLoggingIn: false,
			buttonText: 'Log in'
		}

		this.handleChange = this.handleChange.bind(this);
		this.onLogin = this.onLogin.bind(this);
	}

	handleChange(e) {
		this.setState({
			[e.target.name]: e.target.value
		});
	}	

	onLogin(e) {
		e.preventDefault();

		this.isLoggingIn();

		let that = this;
		$.ajax({
			url: '/login',
			type: 'POST',
			dataType: 'json',
			headers: {
				'X-CSRF-Token': App.csrfToken,
			},
			data: $('#LoginUserForm').serialize(),
			success: function(response) {
				console.log('success', response)

		    	that.setState({
		    		isLoggingIn: false,
		    		buttonText: 'Log in'
		    	})

		        swal({
		            title: "CityCard",
		            text: "Welcome back to CityCard",
		            type: "success",
		            showConfirmButton: true
		        }, function() {
					window.location = '/posts';
				});
			},
			error: function(error)
			{
				console.log('error', error);

		    	that.setState({
		    		isLoggingIn: false,
		    		buttonText: 'Log in',
		    	})

		        swal({
		            title: "CityCard",
		            text: 'Oops. Email or password is incorrect.',
		            type: "error",
		            showConfirmButton: true
		        });	
			}
		})
	}

	isLoggingIn()
	{
		this.setState({
			isLoggingIn: true,
			buttonText: 'Logging in'
		})
	}

	render()
	{
		return (
			<div>
				<div className="Box Registration">
					<h1 className="text-center">
						<img src="/images/logo.svg" alt="CityCard" title="CityCard" className="img-responsive" />
					</h1>
					<form method="POST" id="LoginUserForm" onSubmit={this.onLogin}>
						<div className="form-group">
							<input type="text" 
								name="email" 
								className="form-control" 
								value={this.state.email}
								onChange={this.handleChange}
								placeholder="Email Address" />
						</div>
						
						<div className="form-group">
							<div className="forgot-password--container">
								<input type="password" 
									name="password" 
									className="form-control" 
									value={this.state.password}
									onChange={this.handleChange}
									placeholder="Password" />
									<small>
										<a href="#" onClick={this.props.showForgotPassword}>Forgot?</a>
									</small>
							</div>
						</div>	
						
						<div className="form-group">
							<button type="submit" 
								className="btn btn-block btn-primary"
								onClick={this.onLogin}
								disabled={this.state.isLoggingIn}
							>
								{ this.state.isLoggingIn ?
									<span>&nbsp; <i className="fa fa-spinner fa-spin"></i></span> :
									<span></span>
								}							
								{this.state.buttonText}
							</button>
						</div>
					</form>						
				</div>	

				<div className="Box Login">
					<p>Don't have an account? <a href="#" onClick={this.props.showRegister}>Sign up</a></p>
				</div>	
			</div>
		)
	}
}

export default LoginUser;
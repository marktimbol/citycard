import React from 'react';

class ForgotPassword extends React.Component
{
	constructor(props)
	{
		super(props);

		this.state = {
			email: '',

			isSubmitting: false,
			buttonText: 'Reset Password',

			errors: [],
		}

		this.handleChange = this.handleChange.bind(this);
		this.onSubmit = this.onSubmit.bind(this);
	}

	handleChange(e) {
		this.setState({
			[e.target.name]: e.target.value
		});
	}	

	onSubmit(e)
	{
		e.preventDefault();

		this.isSubmitting();
		
		let that = this;

		$.ajax({
		    url: '/password/email',
		    type: 'POST',
		    dataType: 'json',
		    data: $('#ForgotPasswordForm').serialize(),
		    headers: { 'X-CSRF-Token': App.csrfToken },
		    success: function(response) {
		    	console.log(response);
		    	if( response.success )
		    	{
			    	that.setState({
			    		isSubmitting: false,
			    		buttonText: 'Reset Password'
			    	})

			        swal({
			            title: "CityCard",
			            text: "Thanks! Please check your email for a link to reset your password.",
			            type: "success",
			            showConfirmButton: true
			        });		
		    	} else {
			    	that.setState({
			    		isSubmitting: false,
			    		buttonText: 'Reset Password',
			    		errors: response.errors,
			    	})	
		    	}
		    },
		    error: function(response) {
		    	console.log(response);

		    	that.setState({
		    		isSubmitting: false,
		    		buttonText: 'Reset Password',
		    		errors: response.responseJSON,
		    	})
		    }
		})
	}

	isSubmitting() {
		this.setState({
			isSubmitting: true,
			buttonText: 'Resetting Password'
		})
	}

	render()
	{
		let errors = this.state.errors;
		let emailClass = errors.hasOwnProperty('email') ? 'form-group has-error' : 'form-group';

		return (
			<div>
				<div className="Box Registration">
					<h1 className="text-center">
						<img src="/images/logo.svg" alt="CityCard" title="CityCard" className="img-responsive" />
					</h1>
					<p className="lead text-center">
						We can help you reset your password using your CityCard email address linked to your account.
					</p>
					<div className="Registration__form">
						<form method="POST" id="ForgotPasswordForm" onSubmit={this.onSubmit}>
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

export default ForgotPassword;
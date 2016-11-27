import React, { Component } from 'react';
import ReactDOM from 'react-dom';

class CreateClerk extends Component
{
	constructor(props) {
		super(props);

		this.state = {
			isSubmitted: false,
			submitButtonText: 'Save',

			first_name: '',
			last_name: '',
			phone: '',
			email: '@citycard.me',
			password: '',
			password_confirmation: '',
		}

		this.handleChange = this.handleChange.bind(this);
	}

	handleChange(e) {
		this.setState({
			[e.target.name]: e.target.value
		});
	}

	onSubmit(e) {
		e.preventDefault();
		this.isSubmitting();

		let url = '/dashboard/merchants/' + app.merchant.id + '/clerks';

		$.ajax({
		    url: url,
		    type: 'POST',
		    data: $('#CreateMerchantClerkForm').serialize(),
		    headers: { 'X-CSRF-Token': App.csrfToken },
		    success: function(response) {

				this.setState({
					submitButtonText: 'Save',
					isSubmitted: false
				})

		        swal({
		            title: "City Card",
		            text: "You have successfully created a new clerk",
		            type: "success",
		            showConfirmButton: true
		        }, function() {
					window.location = '/dashboard/merchants/' + response.id;
				});

		    }.bind(this),
		    error: function(error) {
				console.log(error);
				this.resetSubmitButton();
		    }.bind(this)
		});
	}

	isSubmitting() {
		this.setState({
			isSubmitted: true,
			submitButtonText: 'Saving',
		});
	}

	resetSubmitButton() {
		this.setState({
			isSubmitted: false,
			submitButtonText: 'Save',
		});
	}

	render() {
		return (
			<form method="POST" id="CreateMerchantClerkForm" onSubmit={this.onSubmit.bind(this)}>
				<div className="form-group">
					<label htmlFor="merchant">Merchant Name</label>
					<input type="text" value={app.merchant.name} className="form-control" disabled />
				</div>
				<div className="row">
					<div className="col-md-6">
						<div className="form-group">
							<label htmlFor="first_name">First Name</label>
							<input type="text"
								name="first_name"
								id="first_name"
								className="form-control"
								value={this.state.first_name}
								onChange={this.handleChange} />
						</div>
					</div>
					<div className="col-md-6">
						<div className="form-group">
							<label htmlFor="last_name">Last Name</label>
							<input type="text"
								name="last_name"
								id="last_name"
								className="form-control"
								value={this.state.last_name}
								onChange={this.handleChange} />
						</div>
					</div>
				</div>
				<div className="form-group">
					<label htmlFor="phone" className="label-block">Phone</label>
					<input type="tel"
						name="phone"
						id="phone"
						className="form-control"
						value={this.state.phone}
						onChange={this.handleChange} />
				</div>

				<h3>Account Details</h3>

				<div className="form-group">
					<label htmlFor="email">Email</label>
					<input type="email"
						name="email"
						id="email"
						value={this.state.email}
						onChange={this.handleChange}
						className="form-control" />
				</div>

				<div className="row">
					<div className="col-md-6">
						<div className="form-group">
							<label htmlFor="password">Password</label>
							<input type="password"
								name="password"
								id="password"
								className="form-control"
								value={this.state.password}
								onChange={this.handleChange} />
						</div>
					</div>
					<div className="col-md-6">
						<div className="form-group">
							<label htmlhtmlFor="password_confirmation">Password Confirmation</label>
							<input type="password"
								name="password_confirmation"
								id="password_confirmation"
								className="form-control"
								value={this.state.password_confirmation}
								onChange={this.handleChange} />
						</div>
					</div>
				</div>

				<div className="form-group">
					<button
						type="submit"
						className="btn btn-primary"
						onClick={this.onSubmit.bind(this)}
					>
						{ this.state.submitButtonText }
						{ this.state.isSubmitted ? <span>&nbsp; <i className="fa fa-spinner fa-spin"></i></span> : <span></span> }
					</button>
				</div>
			</form>
		)
	}
}

ReactDOM.render(
	<CreateClerk />,
	document.getElementById('CreateClerk')
);

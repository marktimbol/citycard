// component/CreateOutlet.js
import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import Countries from '../Countries';

class CreateOutlet extends Component
{
	constructor(props)
	{
		super(props);

		this.state = {
			isSubmitted: false,
			submitButtonText: 'Save',
			phone: '',
			address1: '',
			address2: '',
			latitude: '',
			longitude: '',
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

		let merchant = app.merchant;
		let url = '/dashboard/merchants/' + merchant.id + '/outlets';

		$.ajax({
		    url: url,
		    type: 'POST',
		    data: $('#CreateOutletForm').serialize(),
		    headers: { 'X-CSRF-Token': App.csrfToken },
		    success: function(response) {

				this.setState({
					submitButtonText: 'Save',
					isSubmitted: false
				})

		        swal({
		            title: "City Card",
		            text: "You have successfully created a new Outlet",
		            type: "success",
		            showConfirmButton: true
		        }, function() {
					window.location = '/dashboard/merchants/' + merchant.id + '/outlets/' + response.id;
				});

		    }.bind(this),
		    error: function(error) {
		    	this.resetSubmitButton();
				let errors = error.responseJSON;
				let errorMessage = '';
				
		        $.each(errors, function(index, value) {
		        	errorMessage += value[0] + '\n';
		        });		

		        swal({
		            title: "City Card",
		            text: errorMessage,
		            type: "error",
		            showConfirmButton: true
		        });	
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
		let merchant = app.merchant;

		return (
			<form method="POST" id="CreateOutletForm" onSubmit={this.onSubmit.bind(this)}>
				<div className="form-group">
					<label htmlFor="merchant">Merchant Name</label>
					<input
						type="text"
						value={merchant.name}
						className="form-control"
						disabled />
				</div>

				<div className="form-group">
					<label htmlFor="phone" className="label-block">Phone</label>
					<input type="tel"
						name="phone"
						id="phone"
						value={this.state.phone}
						onChange={this.handleChange}
						className="form-control" />
				</div>

				<div className="row">
					<div className="col-md-6">
						<div className="form-group">
							<label htmlFor="address1">Address 1</label>
							<input type="text"
								name="address1"
								id="address1"
								value={this.state.address1}
								onChange={this.handleChange}
								className="form-control" />
						</div>
					</div>

					<div className="col-md-6">
						<div className="form-group">
							<label htmlFor="address2">Address 2</label>
							<input type="text"
								name="address2"
								id="address2"
								value={this.state.address2}
								onChange={this.handleChange}
								className="form-control" />
						</div>
					</div>
				</div>

				<div className="row">
					<div className="col-md-6">
						<div className="form-group">
							<label htmlFor="latitude">Latitude</label>
							<input type="text"
								name="latitude"
								id="latitude"
								value={this.state.latitude}
								onChange={this.handleChange}
								className="form-control" />
						</div>
					</div>

					<div className="col-md-6">
						<div className="form-group">
							<label htmlFor="longitude">Longitude</label>
							<input type="text"
								name="longitude"
								id="longitude"
								value={this.state.longitude}
								onChange={this.handleChange}
								className="form-control" />
						</div>
					</div>
				</div>

				<Countries />

				<h2>Account Details</h2>

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
							<label htmlFor="password_confirmation">Password Confirmation</label>
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
	<CreateOutlet />,
	document.getElementById('CreateOutlet')
);

import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import Select, { Creatable } from 'react-select';
import Countries from './Countries';
import Categories from './Categories';

class CreateMerchant extends Component
{
	constructor(props) {
		super(props);

		this.state = {
			isSubmitted: false,
			submitButtonText: 'Save',
			name: '',
			phone: '',
			currency: 'AED',
			area: '',
			email: '@citycard.me',
			password: '',
			password_confirmation: '',
			errors: [],
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

		let url = '/dashboard/merchants/';

		$.ajax({
		    url: url,
		    type: 'POST',
		    data: $('#CreateMerchantForm').serialize(),
		    headers: { 'X-CSRF-Token': App.csrfToken },
		    success: function(response) {
		    	console.log(response);

				this.setState({
					submitButtonText: 'Save',
					isSubmitted: false
				})

		        swal({
		            title: "City Card",
		            text: "You have successfully created a new Merchant",
		            type: "success",
		            showConfirmButton: true
		        }, function() {
					window.location = '/dashboard/merchants/' + response.id;
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
		return (
			<form method="POST" id="CreateMerchantForm" onSubmit={this.onSubmit.bind(this)}>
				<div className="form-group">
					<label htmlFor="name">Name</label>
					<input type="text"
						name="name"
						id="name"
						value={this.state.name}
						onChange={this.handleChange}
						className="form-control" />
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

				<h3>What is the nature of your business?</h3>

				<Categories errors={this.state.errors} />

				<h3>And where it is located?</h3>
				<Countries />

				<h3>What is your preferred currency?</h3>
				<div className="row">
					<div className="col-md-4">
						<input type="text"
							name="currency"
							id="currency"
							value={this.state.currency}
							onChange={this.handleChange}
							className="form-control" />
					</div>
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
	<CreateMerchant />,
	document.getElementById('CreateMerchant')
);

// Outlet/EditOutlet.js
import axios from 'axios';
import ReactDOM from 'react-dom';
import React, { Component } from 'react';

class EditOutlet extends Component
{
	constructor(props)
	{
		super(props);

		let outlet = app.outlet;

		this.state = {
			isSubmitted: false,
			submitButtonText: 'Update',

			name: outlet.name,
			phone: outlet.phone,
			email: outlet.email,
			currency: outlet.currency,

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

		let that = this;
		let url = '/dashboard/merchants/' + app.merchant.id + '/outlets/' + app.outlet.id;
	    axios({
	    	method: 'PUT',
	    	url: url,
	    	headers: {
	    		'X-CSRF-Token': App.csrfToken,
	    	},
	    	data: {
	    		name: that.state.name,
	    		phone: that.state.phone,
	    		email: that.state.email,
	    		currency: that.state.currency,
	    	}
	    }).then(function(response) {
	    	console.log('EditOutlet response', response.data);
	        swal({
	            title: "City Card",
	            text: "You have successfully updated the Outlet information",
	            type: "success",
	            showConfirmButton: true
	        }, function() {
		        window.location = '/dashboard/merchants/' + app.merchant.id + '/outlets/' + app.outlet.id;	            	
	        });		
	    }).catch(function(error) {
	    	console.log('Error', error.response.data);
	    	that.setState({
	    		errors: error.response.data
	    	})
	    	that.resetSubmitButton();
	    })
	}

	isSubmitting()
	{
		this.setState({
			isSubmitted: true,
			submitButtonText: 'Updating',
		});
	}

	resetSubmitButton()
	{
		this.setState({
			isSubmitted: false,
			submitButtonText: 'Save',
		});
	}

	render()
	{
		let outlet = app.outlet;
		let errors = this.state.errors;

		return (
			<form method="POST" id="EditOutletForm" onSubmit={this.onSubmit.bind(this)}>
				<div className="form-group">
					<label htmlFor="merchant">Merchant Name</label>
					<input
						type="text"
						value={app.merchant.name}
						className="form-control"
						disabled />
				</div>			
				<div className={errors.hasOwnProperty('name') ? 'form-group has-error' : 'form-group'}>
					<label>Name</label>
					<input type="text"
						name="name"
						value={this.state.name}
						onChange={this.handleChange}
						className="form-control" />
					<span className="help-block">
						{ errors.hasOwnProperty('name') ? errors['name'] : '' }
					</span>
				</div>

				<div className={errors.hasOwnProperty('phone') ? 'form-group has-error' : 'form-group'}>
					<label htmlFor="phone" className="label-block">Phone</label>
					<input type="tel"
						name="phone"
						id="phone"
						value={this.state.phone}
						onChange={this.handleChange}
						className="form-control" />
					<span className="help-block">
						{ errors.hasOwnProperty('phone') ? errors['phone'] : '' }
					</span>						
				</div>

				<h3>What is your preferred currency?</h3>
				<div className="row">
					<div className="col-md-4">
						<div className={errors.hasOwnProperty('currency') ? 'form-group has-error' : 'form-group'}>
							<label htmlFor="currency" className="control-label">Currency</label>
							<input type="text"
								name="currency"
								id="currency"
								value={this.state.currency}
								onChange={this.handleChange}
								className="form-control" />
							<span className="help-block">
								{ errors.hasOwnProperty('currency') ? errors['currency'] : '' }
							</span>								
						</div>	
					</div>
				</div>

				<h2>Account Details</h2>

				<div className={errors.hasOwnProperty('email') ? 'form-group has-error' : 'form-group'}>
					<label htmlFor="email">Email</label>
					<input type="email"
						name="email"
						id="email"
						value={this.state.email}
						onChange={this.handleChange}
						className="form-control" />
					<span className="help-block">
						{ errors.hasOwnProperty('email') ? errors['email'] : '' }
					</span>						
				</div>

				<div className="form-group">
					<button
						type="submit"
						className="btn btn-primary"
						onClick={this.onSubmit.bind(this)}
						disabled={this.state.isSubmitted}
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
	<EditOutlet />,
	document.getElementById('EditOutlet')
);

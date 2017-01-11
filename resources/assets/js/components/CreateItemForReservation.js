// CreateItemForReservation.js

import React from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';

class CreateItemForReservation extends React.Component
{
	constructor(props) {
		super(props);
		
		this.state = {
			title: '',
			has_options: false,
			reservationOptions: ['Table for 2'],

			buttonText: 'Save',
			is_submitting: false,

			errors: [],
		}

		this.handleHasOptions = this.handleHasOptions.bind(this)
		this.createReservationOption = this.createReservationOption.bind(this)
		this.deleteReservationOption = this.deleteReservationOption.bind(this)
		this.handleReservationOptionChange = this.handleReservationOptionChange.bind(this)
		this.onSubmit = this.onSubmit.bind(this)
	}

	handleHasOptions() {
		this.setState({
			has_options: ! this.state.has_options
		})
	}

    createReservationOption(e) {
    	e.preventDefault();

    	let newOption = ['New Option'];

    	this.setState({
    		reservationOptions: this.state.reservationOptions.concat(newOption)
    	})
    }

    deleteReservationOption(selectedIndex, e) {
    	e.preventDefault();

    	console.log('deleteReservationOption', selectedIndex);

    	this.setState({
    		reservationOptions: this.state.reservationOptions.filter((option, index) => index !== selectedIndex)
    	})
    }	

    handleReservationOptionChange(index, e) {
    	let newState = Object.assign({}, this.state);
    	newState.reservationOptions[index] = e.target.value;

    	this.setState({ newState })
    }	

    onSubmit(e) {
    	e.preventDefault();

    	this.setState({
    		is_submitting: true,
    		buttonText: 'Saving'
    	});

    	let that = this;
    	let url = app.admin_path + '/dashboard/outlets/' + app.outlet_id + '/for-reservations';

    	console.log(url);

    	let reservationOptions = [];
    	if( this.state.has_options ) {
    		reservationOptions = that.state.reservationOptions
    	}

    	let data = {
    		title: that.state.title,
    		has_options: that.state.has_options,
    		reservationOptions: reservationOptions
    	}

    	axios({
    		method: 'POST',
    		url: url,
    		data: data,
    		headers: {
    			'X-CSRF-Token': App.csrfToken,
    		}
    	}).then(function(response) {
    		console.log('success', response)

    		that.setState({
    			is_submitting: false,
    			buttonText: 'Save',

    			reservationOptions: [],
    			has_options: false,
    			title: ''
    		})

	        swal({
	            title: "City Card",
	            text: "You have successfully created an item for reservation.",
	            type: "success",
	            showConfirmButton: true
	        }, function() {
	        	window.location = '/dashboard/merchants/' + app.merchant_id + '/outlets/' + app.outlet_id;
	        });	    		
    	})
    	.catch(function(error) {
    		console.log('error', error);
    		console.log('error data', error.response.data);
    		console.log('error headers', error.response.headers);
    		console.log('error status', error.response.status);

    		that.setState({
    			is_submitting: false,
    			buttonText: 'Save',
    			errors: error.response.data,
    		})   		
    	});    	
    }

	render()
	{
		let errors = this.state.errors;

		let reservationOptions = this.state.reservationOptions.map((reservationOption, index) => {
			let className = errors.hasOwnProperty('reservationOptions.'+index) ? 'form-group input-group has-error' : 'form-group input-group';
			return (
				<div className={className} key={index}>
					<input type="text" 
						name="options[]" 
						id="options" 
						className="form-control" 
						value={reservationOption} 
						onChange={(e) => this.handleReservationOptionChange(index, e)} />
					<span className="input-group-btn">
						<button className="btn bnt-sm btn-default" onClick={this.createReservationOption}>
							<i className="fa fa-plus"></i>
						</button>
						<button className="btn bnt-sm btn-danger" onClick={(e) => this.deleteReservationOption(index, e)}>
							<i className="fa fa-minus"></i>
						</button>						
					</span>
				</div>
			)
		})

		return (
			<form method="POST" onSubmit={this.onSubmit}>
				<div className={errors.hasOwnProperty('title') ? 'form-group has-error' : 'form-group'}>
					<label className="control-label" htmlFor="title">Title</label>
					<input type="text" 
						name="title" 
						id="title" 
						value={this.state.title} 
						onChange={(e) => this.setState({ title: e.target.value })}
						className="form-control" />
				</div>
				<div className="checkbox">
					<label>
						<input type="checkbox" checked={this.state.has_options} onChange={this.handleHasOptions} />Add Option(s)
					</label>
				</div>

				{
					this.state.has_options ? 
						<div className="row">
							<div className="col-md-6">
								{reservationOptions}
							</div>
						</div>
					: <div></div>
				}

				<div className="form-group">
					<button
						type="submit"
						className="btn btn-primary"
						onClick={this.onSubmit.bind(this)}
						disabled={this.state.is_submitting}
					>
						{ this.state.buttonText }
						{ this.state.is_submitting ? <span>&nbsp; <i className="fa fa-spinner fa-spin"></i></span> : <span></span> }
					</button>					
				</div>
			</form>
		)
	}
}

ReactDOM.render(
	<CreateItemForReservation />,
	document.getElementById('CreateItemForReservation')
)
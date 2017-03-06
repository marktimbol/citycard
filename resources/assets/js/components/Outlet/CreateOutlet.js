// component/CreateOutlet.js
import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import Location from '../Location';
import { geocodeByAddress } from 'react-places-autocomplete';

class CreateOutlet extends Component
{
	constructor(props)
	{
		super(props);

		this.state = {
			isSubmitted: false,
			submitButtonText: 'Save',
			email: '@citycard.me',
			phone: '',
			address: '',
			city: '',
			lat: '',
			lng: '',

			errors: [],
		}

		this.handleChange = this.handleChange.bind(this);
		this.handleAddressChange = this.handleAddressChange.bind(this);
		this.handleCityChange = this.handleCityChange.bind(this);
		this.handleAreaChange = this.handleAreaChange.bind(this);  		
	}

	handleChange(e) {
		this.setState({
			[e.target.name]: e.target.value
		});
	}

    handleAddressChange(address) {
        let that = this;
        this.setState({ address })
        
        geocodeByAddress(address, (err, { lat, lng }) => {
            that.setState({ lat, lng })
        })
    }

	handleCityChange(city) {  
		this.setState({ city });
	}

	handleAreaChange(area) {  
		this.setState({ area: area.value });
	}	

	onSubmit(e) {
		e.preventDefault();
		this.isSubmitting();

		let that = this;
		let merchant = app.merchant;

		axios.post('/dashboard/merchants/' + merchant.id + '/outlets', {
			email: this.state.email,
			phone: this.state.phone,
			address: this.state.address,
			city: this.state.city,
			area: this.state.area,
			lat: this.state.lat,
			lng: this.state.lng,
		}).then(function(response) {
			console.log('response', response);

			that.setState({
				submitButtonText: 'Save',
				isSubmitted: false
			})

	        swal({
	            title: "CityCard",
	            text: "You have successfully created a new Outlet",
	            type: "success",
	            showConfirmButton: true
	        });

		}).catch(function(error) {
			console.log('error', error)
			that.setState({
				submitButtonText: 'Save',
				isSubmitted: false
			})
		})
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
					<label htmlFor="email">Email</label>
					<input type="email"
						name="email"
						id="email"
						value={this.state.email}
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

				<Location 
					errors={this.state.errors}
					address=''
					addressClass={this.state.errors.hasOwnProperty('address') ? 'has-error' : ''}
					handleAddressChange={this.handleAddressChange}
					handleCityChange={this.handleCityChange}
					handleAreaChange={this.handleAreaChange} />

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

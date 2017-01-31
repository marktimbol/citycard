import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import Location from '../Location';
import Categories from '../Categories';
import { geocodeByAddress } from 'react-places-autocomplete';

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
			email: '@citycard.me',
			password: '',
			password_confirmation: '',

			selectedCategory: '',
			selectedSubcategories: '',

            address: '',
            lat: '',
            lng: '',
			city: '',
			area: '',

			errors: [],
		}

		this.handleChange = this.handleChange.bind(this);

        this.handleCategoryChange = this.handleCategoryChange.bind(this);
        this.handleSubcategoryChange = this.handleSubcategoryChange.bind(this);  		

		this.handleAddressChange = this.handleAddressChange.bind(this);
		this.handleCityChange = this.handleCityChange.bind(this);
		this.handleAreaChange = this.handleAreaChange.bind(this);        
	}

	handleChange(e) {
		this.setState({
			[e.target.name]: e.target.value
		});
	}

    handleCategoryChange(value) {
    	this.setState({
    		selectedCategory: value
    	})
    }	

    handleSubcategoryChange(value) {        
        this.setState({
            selectedSubcategories: value
        })
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
		axios({
			method: 'POST',
			url: '/dashboard/merchants/',
			headers: {
				'X-CSRF-Token': App.csrfToken,
			},
			data: {
				name: that.state.name,
				email: that.state.email,
				phone: that.state.phone,
				password: that.state.password,
				password_confirmation: that.state.password_confirmation,
				address: that.state.address,
				lat: that.state.lat,
				lng: that.state.lng,
				currency: that.state.currency,
				city: that.state.city,
				area: that.state.area,
				category: that.state.selectedCategory,
				subcategories: that.state.selectedSubcategories
			}
		}).then(function(response) {
			that.setState({
				submitButtonText: 'Save',
				isSubmitted: false
			})
	        swal({
	            title: "City Card",
	            text: "You have successfully created a new Merchant",
	            type: "success",
	            showConfirmButton: true
	        }, function() {
	        	window.location = '/dashboard/merchants/' + response.data.id;
	        });
		}).catch(function(error) {
			console.log('response', error.response.data);
			that.setState({
				submitButtonText: 'Save',
				isSubmitted: false,
				errors: error.response.data
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
		let errors = this.state.errors;
        let formGroup = 'form-group';
        let hasError = formGroup + ' has-error';

		let nameInputClass = errors.hasOwnProperty('name') ? hasError : formGroup;
		let emailInputClass = errors.hasOwnProperty('email') ? hasError : formGroup;
		let phoneInputClass = errors.hasOwnProperty('phone') ? hasError : formGroup;
		let addressInputClass = errors.hasOwnProperty('address') ? hasError : formGroup;
		let currencyInputClass = errors.hasOwnProperty('currency') ? hasError : formGroup;
		let passwordInputClass = errors.hasOwnProperty('password') ? hasError : formGroup;
		let areaInputClass = errors.hasOwnProperty('area') ? hasError : formGroup;

		return (
			<form method="POST" id="CreateMerchantForm" onSubmit={this.onSubmit.bind(this)}>
				<div className={nameInputClass}>
					<label htmlFor="name" className="control-label">Name</label>
					<input type="text"
						name="name"
						id="name"
						value={this.state.name}
						onChange={this.handleChange}
						className="form-control" />
					<span className="help-block">{ errors.hasOwnProperty('name') ? errors['name'] : '' }</span>
				</div>

				<div className={phoneInputClass}>
					<label htmlFor="phone" className="label-block">Phone</label>
					<input type="tel"
						name="phone"
						id="phone"
						value={this.state.phone}
						onChange={this.handleChange}
						className="form-control" />
					<span className="help-block">{ errors.hasOwnProperty('phone') ? errors['phone'] : '' }</span>
				</div>

				<h3>What is the nature of your business?</h3>

				<Categories 
					errors={errors} 
					handleCategoryChange={this.handleCategoryChange}
					handleSubcategoryChange={this.handleSubcategoryChange} />

				<h3>And where it is located?</h3>
				<Location 
					errors={this.state.errors}
					address=''
					addressClass={this.state.errors.hasOwnProperty('address') ? 'has-error' : ''}
					handleAddressChange={this.handleAddressChange}
					handleCityChange={this.handleCityChange}
					handleAreaChange={this.handleAreaChange} />

				<h3>What is your preferred currency?</h3>
				<div className="row">
					<div className="col-md-4">
						<div className={currencyInputClass}>
							<input type="text"
								name="currency"
								id="currency"
								value={this.state.currency}
								onChange={this.handleChange}
								className="form-control" />
							<span className="help-block">{ errors.hasOwnProperty('currency') ? errors['currency'] : '' }</span>
						</div>
					</div>
				</div>

				<h3>Account Details</h3>

				<div className={emailInputClass}>
					<label htmlFor="email">Email</label>
					<input type="email"
						name="email"
						id="email"
						value={this.state.email}
						onChange={this.handleChange}
						className="form-control" />
					<span className="help-block">{ errors.hasOwnProperty('email') ? errors['email'] : '' }</span>
				</div>

				<div className="row">
					<div className="col-md-6">
						<div className={passwordInputClass}>
							<label htmlFor="password">Password</label>
							<input type="password"
								name="password"
								id="password"
								className="form-control"
								value={this.state.password}
								onChange={this.handleChange} />
							<span className="help-block">{ errors.hasOwnProperty('password') ? errors['password'] : '' }</span>
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
	<CreateMerchant />,
	document.getElementById('CreateMerchant')
);

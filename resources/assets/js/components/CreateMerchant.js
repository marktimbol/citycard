import React, { Component } from 'react';
import ReactDOM from 'react-dom';

class CreateMerchant extends Component
{
	constructor(props)
	{
		super(props);

		this.state = {
			isSubmitted: false,
			submitButtonText: 'Save',
			name: '',
			phone: '',
			area: '',
			email: '',
			password: '',
			password_confirmation: '',

			availableCities: [],
			availableAreas: [],
		}

		this.handleChange = this.handleChange.bind(this);
		this.handleCountryChange = this.handleCountryChange.bind(this);
		this.handleCityChange = this.handleCityChange.bind(this);
		this.handleAreaChange = this.handleAreaChange.bind(this);

	}

	handleChange(e) {
		this.setState({
			[e.target.name]: e.target.value
		});
	}

	handleCountryChange(e) {
		let countryId = e.target.value;

		this.fetchCities(countryId);
	}

	fetchCities(countryId) {
		let url = '/api/countries/'+countryId+'/cities';
		$.get(url, function(response) {
			this.setState({
				availableCities: response
			});
			if( this.state.availableCities.length <= 0 ) {
				swal({
					title: "City Card",
					text: "No cities found.",
					type: "error",
					showConfirmButton: true
				});
			}
		}.bind(this))
	}

	handleCityChange(e) {
		let cityId = e.target.value;
		this.fetchAreas(cityId);
	}

	fetchAreas(cityId)
	{
		let url = '/api/cities/'+cityId+'/areas';

		$.get(url, function(response) {
			this.setState({
				availableAreas: response
			});
		}.bind(this))
	}

	handleAreaChange(e) {
		let areaId = e.target.value;
		this.setState({ area: areaId });
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

		        swal({
		            title: "City Card",
		            text: "You have successfully created a new Merchant",
		            type: "success",
		            showConfirmButton: true
		        });

		        this.setState({
		            submitButtonText: 'Save',
		            isSubmitted: false
		        })

		        // window.location = '/dashboard/merchants/' + response.id;

		    }.bind(this),
		    error: function(error) {
				console.log(error);
				this.resetSubmitButton();
		    }.bind(this)
		});
	}

	isSubmitting()
	{
		this.setState({
			isSubmitted: true,
			submitButtonText: 'Saving',
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
		let availableCountries = app.countries.map(country => {
			return (
				<option value={country.id} key={country.id}>
					{country.name}
				</option>
			)
		});

		let availableCities = this.state.availableCities.map((city) => {
			return (
				<option value={city.id} key={city.id}>{city.name}</option>
			)
		});

		let availableAreas = this.state.availableAreas.map((area) => {
			return (
				<option value={area.id} key={area.id}>{area.name}</option>
			)
		});

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

                <div className="row">
					<div className="col-md-4">
						<div className="form-group">
							<label htmlFor="country">Country</label>
							<select
								name="country"
								id="country"
								className="form-control"
								onChange={this.handleCountryChange}
							>
								<option value=""></option>
								{availableCountries}
							</select>
						</div>
					</div>
					<div className="col-md-4">
						<div className="form-group">
							<label htmlFor="city">City</label>
							<select
								name="city"
								id="city"
								className="form-control"
								onChange={this.handleCityChange}
							>
								<option value=""></option>
								{ availableCities }
							</select>
						</div>
					</div>

					<div className="col-md-4">
						<div className="form-group">
							<label htmlFor="area">Area</label>
							<select
								name="area"
								id="area"
								className="form-control"
								onChange={this.handleAreaChange}
							>
								<option value=""></option>
								{ availableAreas }
							</select>
						</div>
					</div>
				</div>

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
	<CreateMerchant />,
	document.getElementById('CreateMerchant')
);

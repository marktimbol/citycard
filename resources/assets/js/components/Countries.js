import React from 'react';
import Select from 'react-select';

class Countries extends React.Component
{
    constructor(props)
    {
        super(props);

        this.state = {
            selectedCountry: '',
            selectedCity: '',
            selectedArea: '',

            availableCities: [],
            availableAreas: [],
        }

        this.handleCountryChange = this.handleCountryChange.bind(this);
		this.handleCityChange = this.handleCityChange.bind(this);
		this.handleAreaChange = this.handleAreaChange.bind(this);
    }

    handleCountryChange(e) {
        let selectedCountry = e.value;

        this.setState({ selectedCountry });
        this.fetchCities(selectedCountry);
    }

    fetchCities(country) {
		let url = '/api/countries/'+country+'/cities';
		$.get(url, function(response) {
			this.setState({
				availableCities: response
			});
		}.bind(this))
	}

    handleCityChange(e) {
		let selectedCity = e.value;

        this.setState({ selectedCity })
		this.fetchAreas(selectedCity);
	}

	fetchAreas(city) {
		let url = '/api/cities/'+city+'/areas';

		$.get(url, function(response) {
			this.setState({
				availableAreas: response
			});
		}.bind(this))
	}

	handleAreaChange(e) {
		let selectedArea = e.value;
		this.setState({ selectedArea });
	}

    render()
    {        
        let availableCountries = app.countries.map(country => {
            return {
                value: country.id,
                label: country.name
            }
		});

        let availableCities = this.state.availableCities.map(city => {
            return {
                value: city.id,
                label: city.name
            }
		});

        let availableAreas = this.state.availableAreas.map(area => {
            return {
                value: area.id,
                label: area.name
            }
		});

        return (
            <div className="row">
                <div className="col-md-4">
                    <div className="form-group">
                        <label htmlFor="country">Country</label>
                        <Select
                            name="country"
                            value={this.state.selectedCountry}
                            options={availableCountries}
                            onChange={this.handleCountryChange} />
                    </div>
                </div>
                <div className="col-md-4">
                    <div className="form-group">
                        <label htmlFor="city">City</label>
                        <Select
                            name="city"
                            value={this.state.selectedCity}
                            options={availableCities}
                            onChange={this.handleCityChange} />
                    </div>
                </div>

                <div className="col-md-4">
                    <div className="form-group">
                        <label htmlFor="area">Area</label>
                        <Select
                            name="area"
                            value={this.state.selectedArea}
                            options={availableAreas}
                            onChange={this.handleAreaChange} />
                    </div>
                </div>
            </div>
        )
    }
}

export default Countries;

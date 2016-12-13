import React from 'react';
import Select, { Creatable } from 'react-select';

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

            isFetchingCities: false,
            isFetchingAreas: false
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
        this.setState({
            isFetchingCities: true
        })

		let url = '/api/countries/'+country+'/cities';
		$.get(url, function(response) {
			this.setState({
				availableCities: response,
                isFetchingCities: false,
			});
		}.bind(this))
	}

    handleCityChange(e) {
		let selectedCity = e.value;

        this.setState({ selectedCity })
		this.fetchAreas(selectedCity);
	}

	fetchAreas(city) {
        this.setState({
            isFetchingAreas: true
        })

		let url = '/api/cities/'+city+'/areas';

		$.get(url, function(response) {
			this.setState({
				availableAreas: response,
                isFetchingAreas: false
			});
		}.bind(this))
	}

	handleAreaChange(value) {
        console.log(value);
		this.setState({
            selectedArea: value
        });
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
                            isLoading={this.state.isFetchingCities}
                            onChange={this.handleCityChange} />
                    </div>
                </div>

                <div className="col-md-4">
                    <div className="form-group">
                        <label htmlFor="area">Area</label>
                        <Creatable
                            name="area"
                            value={this.state.selectedArea}
                            options={availableAreas}
                            isLoading={this.state.isFetchingAreas}
                            onChange={this.handleAreaChange} />
                    </div>
                </div>
            </div>
        )
    }
}

export default Countries;

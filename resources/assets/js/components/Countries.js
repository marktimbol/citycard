import React from 'react';
import Select, { Creatable } from 'react-select';
import PlacesAutocomplete from 'react-places-autocomplete';
import { geocodeByAddress } from 'react-places-autocomplete';

class Countries extends React.Component
{
    constructor(props)
    {
        super(props);

        this.state = {
            selectedCountry: '',
            selectedCity: '',
            selectedArea: '',
            customArea: false,

            address: '',
            lat: '',
            lng: '',            

            availableCities: [],
            availableAreas: [],

            isFetchingCities: false,
            isFetchingAreas: false
        }

        this.handleAddressChange = this.handleAddressChange.bind(this);
        this.handleCountryChange = this.handleCountryChange.bind(this);
		this.handleCityChange = this.handleCityChange.bind(this);
		this.handleAreaChange = this.handleAreaChange.bind(this);
    }

    handleAddressChange(address) {
        let that = this;
        this.setState({ address })
        
        geocodeByAddress(address, (err, { lat, lng }) => {
            that.setState({ lat, lng })
        })
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

        let that = this;
		let url = '/api/countries/'+country+'/cities';

        axios.get(url)
            .then(function(response) {
                that.setState({
                    availableCities: response.data,
                    isFetchingCities: false,
                });   
            })
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

        let that = this;
		let url = '/api/cities/'+city+'/areas';

        axios.get(url)
            .then(function(response) {
                that.setState({
                    availableAreas: response.data,
                    isFetchingAreas: false
                });
            })
	}

	handleAreaChange(value) {        
        if( isNaN(value.value) ) {
            // User typed the area
            this.setState({
                customArea: true
            })
        } else {
            // User select from the area
            this.setState({
                customArea: false
            })
        }

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

        const addressSuggestions = ({ suggestion }) => (<div><i className="fa fa-map-marker"/>{suggestion}</div>)
        const addressInputClasses = {
            root: 'form-group',
            label: 'form-label',
            input: 'form-control',
        }

        return (
            <div className="row">
                <div className="col-md-12">
                    <PlacesAutocomplete 
                        value={this.state.address} 
                        onChange={this.handleAddressChange}
                        classNames={addressInputClasses}
                        autocompleteItem={addressSuggestions} />
                </div>            
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
                        <input type="hidden" name="custom_area" value={this.state.customArea} />
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

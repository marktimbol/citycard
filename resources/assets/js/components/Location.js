import React from 'react';
import axios from 'axios';
import Select, { Creatable } from 'react-select';
import PlacesAutocomplete from 'react-places-autocomplete';

class Location extends React.Component
{
    constructor(props)
    {
        super(props);

        this.state = {
            selectedCountry: '',
            selectedCity: '',
            selectedArea: '',

            address: this.props.address,

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
        this.setState({
            address
        })
        this.props.handleAddressChange(address);
    }

    handleCountryChange(e) {
        let selectedCountry = e.value;
        this.setState({ selectedCountry });
        this.fetchCities(selectedCountry);
    }

    fetchCities(country) {
        this.setState({ isFetchingCities: true })

        let that = this;
        axios.get('/api/countries/'+country+'/cities')
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

        this.props.handleCityChange(selectedCity);
	}

	fetchAreas(city) {
        this.setState({ isFetchingAreas: true })

        let that = this;
        axios.get('/api/cities/'+city+'/areas')
            .then(function(response) {
                that.setState({
                    availableAreas: response.data,
                    isFetchingAreas: false
                });
            })
	}

	handleAreaChange(value) {  
		this.setState({
            selectedArea: value
        });

        this.props.handleAreaChange(value);
	}

    render()
    {
        let errors = this.props.errors;
        let formGroup = 'form-group';
        let hasError = formGroup + ' has-error';

        let areaInputClass = errors.hasOwnProperty('area') ? hasError : formGroup;

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
                value: area.name,
                label: area.name
            }
		});

        const addressSuggestions = ({ suggestion }) => (<div><i className="fa fa-map-marker"/>{suggestion}</div>)
        let formGroupClass = 'form-group ' + this.props.addressClass;

        const addressInputClasses = {
            root: formGroupClass,
            label: 'form-label control-label',
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
                    <div className={areaInputClass}>
                        <label htmlFor="area" className="control-label">Area</label>
                        <Creatable
                            name="area"
                            value={this.state.selectedArea}
                            options={availableAreas}
                            isLoading={this.state.isFetchingAreas}
                            onChange={this.handleAreaChange} />
                        <span className="help-block">{ errors.hasOwnProperty('area') ? errors['area'] : '' }</span>
                    </div>
                </div>
            </div>
        )
    }
}

export default Location;

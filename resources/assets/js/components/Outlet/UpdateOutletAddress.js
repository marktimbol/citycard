import React from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import Select, { Creatable } from 'react-select';
import PlacesAutocomplete from 'react-places-autocomplete';
import { geocodeByAddress } from 'react-places-autocomplete'

class UpdateOutletAddress extends React.Component
{
    constructor(props)
    {
        super(props);

        this.state = {
            isSubmitted: false,
            submitButtonText: 'Update Location',

            selectedCountry: '',
            selectedCity: '',
            selectedArea: '',

            address: '',
            lat: '',
            lng: '',

            availableCities: [],
            availableAreas: [],

            isFetchingCities: false,
            isFetchingAreas: false,

            errors: []
        }

        this.handleAddressChange = this.handleAddressChange.bind(this);
        this.handleCountryChange = this.handleCountryChange.bind(this);
		this.handleCityChange = this.handleCityChange.bind(this);
		this.handleAreaChange = this.handleAreaChange.bind(this);
    }

    handleAddressChange(address) {
        let that = this;
        this.setState({ address })

        geocodeByAddress(address,  (err, { lat, lng }) => {
            that.setState({ lat, lng })
        })
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

	handleAreaChange(area) {  
		this.setState({
            selectedArea: area
        });
	}

    onSubmit(e) {
        e.preventDefault();
        this.isSubmitting();

        let that = this;        
        axios({
            method: 'PUT',
            url: '/dashboard/outlets/' + app.outlet_id + '/location',
            headers: {
                'X-CSRF-Token': App.csrfToken,
            },
            data: {
                address: that.state.address,
                lat: that.state.lat,
                lng: that.state.lng,
                city: that.state.selectedCity,
                area: that.state.selectedArea,
            }
        }).then(function(response) {
            that.setState({
                isSubmitted: false,
                submitButtonText: 'Update Location'
            })

            swal({
                title: "City Card",
                text: "You have successfully updated the location of the Outlet",
                type: "success",
                showConfirmButton: true
            }, function() {
                window.location = '/dashboard/merchants/' + response.data.merchant_id + '/outlets/' + response.data.outlet_id;
            });            
        })
    }


    isSubmitting() {
        this.setState({
            isSubmitted: true,
            submitButtonText: 'Updating Location',
        });
    }

    render()
    {
        let errors = this.state.errors;
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

        const addressSuggestions = ({ suggestion }) => (<div><i className="fa fa-map-marker"></i> {suggestion}</div>)

        const addressInputClasses = {
            root: 'form-group',
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
                <div className="col-md-12">          
                    <div className="form-group">
                        <label htmlFor="country">Country</label>
                        <Select
                            name="country"
                            value={this.state.selectedCountry}
                            options={availableCountries}
                            onChange={this.handleCountryChange} />
                    </div>
                </div>

                <div className="col-md-6">
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

                <div className="col-md-6">
                    <div className={areaInputClass}>
                        <label htmlFor="area" className="control-label">Area</label>
                        <Creatable
                            name="area"
                            value={this.state.selectedArea}
                            options={availableAreas}
                            isLoading={this.state.isFetchingAreas}
                            onChange={this.handleAreaChange} />
                    </div>
                </div>

                <div className="col-md-12">
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
                </div>
            </div>
        )
    }
}

ReactDOM.render(
    <UpdateOutletAddress />,
    document.getElementById('UpdateOutletAddress')
)

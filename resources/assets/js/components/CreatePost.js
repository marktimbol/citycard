import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import Select from 'react-select';
import DatePicker from 'react-datepicker';
import Dropzone from 'dropzone';
import moment from 'moment';
import Categories from './Categories';

let CreatePostPhotos;

class CreatePost extends Component
{
	constructor(props) {
		super(props);

		this.state = {
			isSubmitted: false,
			submitButtonText: 'Save',
			isExternal: 0,
			source: '',
			source_from: '',
			source_link: '',
			type: '',
			event_date: moment().format('YYYY-MM-DD'),
			event_time: '',
			outlets: [],
			title: '',
			desc: '',
			outlet_ids: '',
			allow_for_reservation: false,
			selectedCategory: '',
			selectedSubcategories: '',
			errors: [],
		}

		this.handleChange = this.handleChange.bind(this);
		this.handleSourceChange = this.handleSourceChange.bind(this);
		this.handleSourceFromChange = this.handleSourceFromChange.bind(this);
		this.handleTypeChange = this.handleTypeChange.bind(this);
		this.handleEventDateChange = this.handleEventDateChange.bind(this);
		this.handleSelectedOutletsChange = this.handleSelectedOutletsChange.bind(this);
		this.handleAllowForReservation = this.handleAllowForReservation.bind(this);

        this.handleCategoryChange = this.handleCategoryChange.bind(this);
        this.handleSubcategoryChange = this.handleSubcategoryChange.bind(this);   
	}

	componentDidMount() {		
		let that = this;

		Dropzone.autoDiscover = false;
		CreatePostPhotos = new Dropzone('div#CreatePostPhotos', {
			url: '/dashboard/merchants/' + app.merchant.id + '/posts/',
			autoProcessQueue: false,
			uploadMultiple: true,
			parallelUploads: 5,
			maxFilesize: 1, // 1 MB
		    headers: {
		    	'X-CSRF-Token': App.csrfToken
		    },
		    init: function() {
				this.on('sending', function(file, xhr, formData) {
					that.isSubmitting();
					
					let outlet_ids = [];
					if( that.state.outlet_ids.length != '' ) {					
						that.state.outlet_ids.map(outlet => {
							outlet_ids.push(outlet.value);
						});
					}

					let subcategories = [];
					if( that.state.selectedSubcategories != '' ) {					
						that.state.selectedSubcategories.map(subcategory => {
							subcategories.push(subcategory.value);
						});	
					}				

					formData.append('isExternal', that.state.isExternal);
					formData.append('source', that.state.source);
					formData.append('source_from', that.state.source_from);
					formData.append('source_link', that.state.source_link);
					formData.append('type', that.state.type);
					formData.append('event_date', that.state.event_date);
					formData.append('event_time', that.state.event_time);
					formData.append('title', that.state.title);
					formData.append('desc', that.state.desc);
					formData.append('outlet_ids', outlet_ids.join());
					formData.append('allow_for_reservation', that.state.allow_for_reservation);
					formData.append('category', that.state.selectedCategory);
					formData.append('subcategories', subcategories.join());
				});		

				this.on('success', function(response, serverResponse) {

					that.setState({
						submitButtonText: 'Save',
						isSubmitted: false
					})

			        swal({
			            title: "City Card",
			            text: "You have successfully created a post.",
			            type: "success",
			            showConfirmButton: true
			        }, function() {
						window.location = '/dashboard/merchants/' + app.merchant.id + '/posts/' + serverResponse.id;
					});					
				})

				this.on('queuecomplete', function(response) {
					console.log('queuecomplete', response);
				})

				// per file
				this.on('error', function(response, errorMessage, xhr) {
					that.resetSubmitButton();
					that.setState({ errors: errorMessage });
				})	    	
		    }
		});
	}

	handleChange(e) {
		this.setState({
			[e.target.name]: e.target.value
		});
	}

	handleSourceChange(e) {
		let source = e.value;
		if( source == 'external' ) {
			this.setState({
				isExternal: 1
			})
		} else {
			this.setState({
				isExternal: 0
			})
		}

		this.setState({ source })
	}

	handleSourceFromChange(e) {
		let source_from = e.value;
		this.setState({
			source_from
		})
	}

	handleTypeChange(e) {
		let type = e.value;
		this.setState({ type })
	}

	handleEventDateChange(date) {
		// console.log(date, date.format('YYYY-MM-DD'), date.toString(), date.toDate(), date.toISOString());

		this.setState({
			event_date: date,
		})
	}

	handleSelectedOutletsChange(values) {
		this.setState({
			outlet_ids: values
		})
	}

	handleAllowForReservation() {
		this.setState({
			allow_for_reservation: ! this.state.allow_for_reservation
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

	onSubmit(e) {
		e.preventDefault();

		CreatePostPhotos.processQueue();
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

	isNotFromCityCard() {
		return this.state.source == 'external' ? true : false
	}

	isEvent() {
		return this.state.type == 'events' ? true : false;
	}	

	render() {
		let availableOutlets = []
		app.outlets.map(outlet => {
			availableOutlets.push({
				value: outlet.id,
				label: outlet.name
			})
		})

		let availableSources = [
			{ value: 'citycard', label: 'City Card' },
			{ value: 'external', label: 'External' }
		]

		let availableSourcesFrom = [];
		app.sources.map(source => {
			availableSourcesFrom.push({
				value: source.id,
				label: source.name
			})
		})

		let availablePostTypes = [
			{ value: 'newsfeed', label: 'News Feed' },
			{ value: 'deals', label: 'Deals' },
			{ value: 'events', label: 'Events' },
		]

		let errors = this.state.errors;
		let sourceClass = errors.hasOwnProperty('source') ? 'form-group has-error' : 'form-group';
		let sourceFromClass = errors.hasOwnProperty('source_from') ? 'form-group has-error' : 'form-group';
		let sourceLinkClass = errors.hasOwnProperty('source_link') ? 'form-group has-error' : 'form-group';
		let postTypeClass = errors.hasOwnProperty('type') ? 'form-group has-error' : 'form-group';
		let eventDateClass = errors.hasOwnProperty('event_date') ? 'form-group has-error' : 'form-group';
		let eventTimeClass = errors.hasOwnProperty('event_time') ? 'form-group has-error' : 'form-group';
		let selectOutletsClass = errors.hasOwnProperty('outlet_ids') ? 'form-group has-error' : 'form-group';
		let titleClass = errors.hasOwnProperty('title') ? 'form-group has-error' : 'form-group';
		let descriptionClass = errors.hasOwnProperty('desc') ? 'form-group has-error' : 'form-group';

		return (
			<form method="POST" id="CreatePostForm" onSubmit={this.onSubmit.bind(this)}>			
				<input type="hidden" name="isExternal" id="isExternal" value={this.state.isExternal} />
				<div className="form-group">
					<label htmlFor="merchant">Merchant Name</label>
					<input
						type="text"
						name="merchant"
						id="merchant"
						className="form-control"
						value={app.merchant.name} disabled />
				</div>

				<div className="row">
					<div className="col-md-4">
						<div className={sourceClass}>
							<label htmlFor="source" className="control-label">Source</label>
							<Select
								name="source"
								value={this.state.source}
								options={availableSources}
								onChange={this.handleSourceChange} />
							{ errors.hasOwnProperty('source') ?
								<span className="help-block">{ errors['source'] }</span>
								: <span></span>
							}								
						</div>
					</div>
					<div className="col-md-8">
						{ this.isNotFromCityCard() ?
							<div className="row">
								<div className="col-md-6">
									<div className={sourceFromClass}>
										<label htmlFor="source_from" className="control-label">From</label>
										<Select
											name="source_from"
											value={this.state.source_from}
											options={availableSourcesFrom}
											onChange={this.handleSourceFromChange} />
										{ errors.hasOwnProperty('source_from') ?
											<span className="help-block">{ errors['source_from'] }</span>
											: <span></span>
										}												
									</div>
								</div>
								<div className="col-md-6">
									<div className={sourceLinkClass}>
										<label htmlFor="source_link" className="control-label">Link</label>
										<input
											type="text"
											name="source_link"
											id="source_link"
											placeholder="eg. http://google.com"
											className="form-control"
											value={this.state.source_link}
											onChange={this.handleChange} />
										{ errors.hasOwnProperty('source_link') ?
											<span className="help-block">{ errors['source_link'] }</span>
											: <span></span>
										}
									</div>
								</div>
							</div>
							: <div></div>
						}
					</div>
				</div>

				<div className={postTypeClass}>
					<label htmlFor="type" className="control-label">Post Type</label>
					<Select
						name="type"
						value={this.state.type}
						options={availablePostTypes}
						onChange={this.handleTypeChange} />
					{ errors.hasOwnProperty('type') ?
						<span className="help-block">{ errors['type'] }</span>
						: <span></span>
					}							
				</div>

				{
					this.isEvent() ?				
						<div className="row">
							<div className="col-md-6">
								<div className={eventDateClass}>
									<label htmlFor="event_date" className="control-label">Event Date</label>
									<input type="hidden" name="event_date" value={this.state.event_date.format('YYYY-MM-DD')} />
									<DatePicker
										dateFormat="YYYY-MM-DD"
										selected={this.state.event_date}
										onChange={this.handleEventDateChange}
										className="form-control"
										minDate={moment()}
										monthsShown={2} />
									{ errors.hasOwnProperty('event_date') ?
										<span className="help-block">{ errors['event_date'] }</span>
										: <span></span>
									}	
								</div>
							</div>

							<div className="col-md-6">
								<div className={eventTimeClass}>
									<label htmlFor="event_time" className="control-label">Event Time</label>
									<input type="text"
										name="event_time"
										id="event_time"
										value={this.state.event_time}
										onChange={this.handleChange}
										className="form-control" />
									{ errors.hasOwnProperty('event_time') ?
										<span className="help-block">{ errors['event_time'] }</span>
										: <span></span>
									}	
								</div>
							</div>
						</div>
					: <div></div>
				}

				<div className={selectOutletsClass}>
					<label htmlFor="outlet_ids" className="control-label">Select Outlets</label>
					<Select
						name="outlet_ids"
						multi={true}
						value={this.state.outlet_ids}
						joinValues
						options={availableOutlets}
						onChange={this.handleSelectedOutletsChange} />
					{ errors.hasOwnProperty('outlet_ids') ?
						<span className="help-block">{ errors['outlet_ids'] }</span>
						: <span></span>
					}						
				</div>

				<Categories errors={errors} 
					selectedCategory={this.state.selectedCategory}
					selectedSubcategories={this.state.selectedSubcategories}
					handleCategoryChange={this.handleCategoryChange}
					handleSubcategoryChange={this.handleSubcategoryChange} />

				<div className={titleClass}>
					<label htmlFor="title" className="control-label">Title</label>
					<input type="text"
						name="title"
						id="title"
						value={this.state.title}
						onChange={this.handleChange}
						className="form-control" />
					{ errors.hasOwnProperty('title') ?
						<span className="help-block">{ errors['title'] }</span>
						: <span></span>
					}	
				</div>

				<div className={descriptionClass}>
					<label htmlFor="editor" className="control-label">Description</label>
					<textarea
						name="desc"
						id="editor"
						className="form-control"
						defaultValue={this.state.desc}
						onChange={this.handleChange}
					></textarea>
					{ errors.hasOwnProperty('desc') ?
						<span className="help-block">{ errors['desc'] }</span>
						: <span></span>
					}					
				</div>

				<div className="form-group">
					<div className="dropzone" id="CreatePostPhotos"></div>
				</div>

				<div className="checkbox">
					<label>
						<input type="checkbox" 
							name="allow_for_reservation" 
							id="allow_for_reservation" 
							checked={this.state.allow_for_reservation} 
							onChange={this.handleAllowForReservation} />
							Available for Reservation
					</label>
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
	<CreatePost />,
	document.getElementById('CreatePost')
);

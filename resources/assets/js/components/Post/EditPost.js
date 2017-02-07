// EditPost.js
import React from 'react';
import axios from 'axios';
import moment from 'moment';
import ReactDOM from 'react-dom';
import Select from 'react-select';
import DatePicker from 'react-datepicker';
import EditCategories from './EditCategories';

class EditPost extends React.Component
{
	constructor(props) {
		super(props);
		
		this.state = {
			isSubmitted: false,
			submitButtonText: 'Update',

			title: app.post.title,
			type: app.post.type,
			desc: app.post.desc,

			event_date: app.post.type == 'events' ? moment() : moment(),
			event_time: app.post.type == 'events' ? app.post.event_time : '',
			event_location: app.post.type == 'events' ? app.post.event_location : '',

			isExternal: app.post.isExternal,

			source: app.post.isExternal == true ? 'external' : 'citycard',
			source_from: app.post.isExternal == true ? app.post.sources[0].name : '',
			source_link: app.post.isExternal == true ? app.post.sources[0].pivot.link : '',

			currentOutlets: '',

			category: app.post.category.id,
			subcategories: [],

			errors: [],
		}

		this.onSubmit = this.onSubmit.bind(this);
		this.handleSourcesChange = this.handleSourcesChange.bind(this);
	}

	componentDidMount() {

		console.log(app.post);
		
		let that = this;
		let currentOutlets = [];
		app.post.outlets.map(outlet => {
			currentOutlets.push({
				value: outlet.id,
				label: outlet.name
			})
		})

		this.setState({ currentOutlets })

		let subcategories = [];
        app.post.subcategories.map(subcategory => {
            subcategories.push({
                value: subcategory.name,
                label: subcategory.name,
            })
        })

        this.setState({ subcategories });	

		$('#editor').on('froalaEditor.contentChanged', function (e, editor) {
			that.setState({
				desc: e.target.value
			})
		});
	}

	handleSourcesChange(e)
	{
		let source = e.value;
		let isExternal = source == 'external' ? true : false;
		this.setState({ isExternal, source })
	}

	onSubmit(e)
	{
		e.preventDefault();
		this.setState({
			isSubmitted: true,
			submitButtonText: 'Updating'
		})

		let that = this;
		axios({
			method: 'PUT',
			url: '/dashboard/merchants/' + app.merchant.id + '/posts/' + app.post.id,
			headers: {
				'X-CSRF-Token': App.csrfToken,
			},
			data: {
				title: that.state.title,
				type: that.state.type,
				desc: that.state.desc,
				isExternal: that.state.isExternal,
				source_from: that.state.source_from,
				source_link: that.state.source_link,
				outlets: that.state.currentOutlets,
				category: that.state.category,
				subcategories: that.state.subcategories,
				event_date: that.state.event_date.format('YYYY-MM-DD'),
				event_time: that.state.event_time,
				event_location: that.state.event_location,
			}
		}).then(function(response) {
			console.log(response, response.data);
			that.resetButton();
	        swal({
	            title: "CityCard",
	            text: "You have successfully updated a post.",
	            type: "success",
	            showConfirmButton: true
	        });	
		}).catch(function(error) {
			console.log(error, error.response.data);	
			that.setState({
				errors: error.response.data
			})
			that.resetButton();				
		})
	}

	resetButton()
	{
		this.setState({
			isSubmitted: false,
			submitButtonText: 'Update'
		})
	}

	render()
	{
		let availableSources = [
			{ value: 'citycard', label: 'CityCard' },
			{ value: 'external', label: 'External' }
		]

		let availableSourcesFrom = [];
		app.sources.map(source => {
			availableSourcesFrom.push({
				value: source.name,
				label: source.name
			})
		})

		let postTypeOptions = [
			{ value: 'newsfeed', label: 'News Feed' },
			{ value: 'deals', label: 'Deals' },
			{ value: 'events', label: 'Events' },
		]

		let availableOutlets = []
		app.outlets.map(outlet => {
			availableOutlets.push({
				value: outlet.id,
				label: outlet.name
			})
		})	

		return (
			<form method="POST" onSubmit={this.onSubmit}>			
				<div className="form-group">
					<label htmlFor="merchant">Merchant Name</label>
					<input type="text" className="form-control" value={app.merchant.name} disabled />
				</div>

				<div className="row">
					<div className="col-md-4">
						<div className="form-group">
							<label htmlFor="source" className="control-label">Source</label>
							<Select value={this.state.source} options={availableSources} onChange={this.handleSourcesChange} />
						</div>
					</div>
					{
						this.state.isExternal ?					
						<div className="col-md-8">
							<div className="row">
								<div className="col-md-6">
									<div className={this.state.errors.hasOwnProperty('source_from') ? 'form-group has-error' : 'form-group'}>
										<label htmlFor="source_from" className="control-label">From</label>
										<Select name="source_from" 
											value={this.state.source_from} 
											options={availableSourcesFrom}
											onChange={(e) => this.setState({ source_from: e.value}) } />										
									</div>
								</div>
								<div className="col-md-6">
									<div className={this.state.errors.hasOwnProperty('source_link') ? 'form-group has-error' : 'form-group'}>
										<label htmlFor="source_link" className="control-label">Link</label>
										<input type="text" 
											className="form-control" 
											value={this.state.source_link} 
											onChange={(e) => this.setState({ source_link: e.target.value }) } />
									</div>
								</div>
							</div>
						</div>
						: <span></span>
					}
				</div>

				<div className="form-group">
					<label htmlFor="type" className="control-label">Post Type</label>
					<Select value={this.state.type} options={postTypeOptions} onChange={(e) => this.setState({ type: e.value })} />					
				</div>

				{ this.state.type == 'events' ?
					<div>		
						<div className="row">
							<div className="col-md-6">
								<div className="form-group">
									<label htmlFor="event_date" className="control-label">Event Date</label>
									<DatePicker
										dateFormat="YYYY-MM-DD"
										selected={this.state.event_date}
										onChange={(date) => this.setState({ event_date: date })}
										className="form-control"
										minDate={moment()}
										monthsShown={2} />	
								</div>
							</div>
							<div className="col-md-6">
								<div className={this.state.errors.hasOwnProperty('event_time') ? 'form-group has-error' : 'form-group'}>
									<label className="control-label">Event Time</label>
									<input type="text"
										value={this.state.event_time}
										className="form-control"
										onChange={(e) => this.setState({ event_time: e.target.value })} />	
								</div>
							</div>
						</div>
						<div className="row">
							<div className="col-md-6">
								<div className={this.state.errors.hasOwnProperty('event_location') ? 'form-group has-error' : 'form-group'}>
									<label htmlFor="event_location" className="control-label">Event Location</label>
									<input type="text"
										value={this.state.event_location}
										className="form-control"
										onChange={(e) => this.setState({ event_location: e.target.value })} />
								</div>
							</div>								
						</div>
					</div>				
					: <span></span>
				}

				<div className={this.state.errors.hasOwnProperty('outlets') ? 'form-group has-error' : 'form-group'}>
					<label htmlFor="outlets" className="control-label">Select Outlets</label>
					<Select
						multi={true}
						joinValues
						value={this.state.currentOutlets} 
						options={availableOutlets} 
						onChange={(currentOutlets) => this.setState({ currentOutlets })} />
				</div>

	            <EditCategories 
	            	errors={this.state.errors}
	            	currentCategory={app.post.category.id}
	            	currentSubcategories={app.post.subcategories}
	            	handleCategoryChange={(category) => this.setState({ category })}
	            	handleSubcategoryChange={(subcategories) => this.setState({ subcategories })} />

				<div className={this.state.errors.hasOwnProperty('title') ? 'form-group has-error' : 'form-group'}>
					<label htmlFor="title" className="control-label">Title</label>
					<input type="text" name="title" id="title" className="form-control" value={this.state.title} onChange={(e) => this.setState({ title: e.target.value })} />
				</div>

				<div className={this.state.errors.hasOwnProperty('desc') ? 'form-group has-error' : 'form-group'}>
					<label htmlFor="editor" className="control-label">Description</label>
					<textarea name="desc" id="editor" className="form-control" defaultValue={this.state.desc} onChange={(e) => this.setState({ desc: e.target.value }) }></textarea>				
				</div>

				<div className="form-group">
					<button type="submit"
						className="btn btn-primary"
						onClick={this.onSubmit}
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
	<EditPost />,
	document.getElementById('EditPost')
);

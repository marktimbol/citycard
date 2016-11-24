import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import Select from 'react-select';

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
			outlets: [],
			selectedCategory: '',
			selectedSubcategories: '',
			availableSubcategories: [],
			isFetchingSubcategories: false,
			title: '',
			desc: '',
			outlet_ids: [],
		}

		this.handleChange = this.handleChange.bind(this);
		this.handleSourceChange = this.handleSourceChange.bind(this);
		this.handleSourceFromChange = this.handleSourceFromChange.bind(this);
		this.handleTypeChange = this.handleTypeChange.bind(this);
		this.handleSelectedOutletsChange = this.handleSelectedOutletsChange.bind(this);
		this.handleCategoryChange = this.handleCategoryChange.bind(this);
		this.handleSubcategoryChange = this.handleSubcategoryChange.bind(this);
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

	handleSelectedOutletsChange(value) {
		this.setState({
			outlet_ids: value
		})
	}

	handleCategoryChange(e) {
		let selectedCategory = e.value;
		this.setState({ selectedCategory, isFetchingSubcategories: true });
		this.fetchSubcategories(selectedCategory);
	}

	fetchSubcategories(selectedCategory) {
		let url = '/api/categories/'+selectedCategory+'/subcategories';

		$.get(url, function(response) {
			this.setState({
				availableSubcategories: response,
				isFetchingSubcategories: false
			});
		}.bind(this))
	}

	handleSubcategoryChange(value) {
		this.setState({
			selectedSubcategories: value
		})
	}

	onSubmit(e) {
		e.preventDefault();
		this.isSubmitting();
		console.log('CreatePostForm' + $('#CreatePostForm').serialize());

		let merchant = app.merchant;
		let url = '/dashboard/merchants/' + merchant.id + '/posts/';

		$.ajax({
		    url: url,
		    type: 'POST',
		    data: $('#CreatePostForm').serialize(),
		    headers: { 'X-CSRF-Token': App.csrfToken },
		    success: function(response) {

				this.setState({
					submitButtonText: 'Save',
					isSubmitted: false
				})

		        swal({
		            title: "City Card",
		            text: "You have successfully created a post.",
		            type: "success",
		            showConfirmButton: true
		        }, function() {
					window.location = '/dashboard/merchants/' + merchant.id + '/posts/' + response.id;
				});

		    }.bind(this),
		    error: function(error) {
				console.log(error);
				this.resetSubmitButton();
		    }.bind(this)
		});
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

	isFromCityCard() {
		return this.state.source == 'citycard' ? true : false
	}

	isNotFromCityCard() {
		return this.state.source == 'external' ? true : false
	}

	isNotification() {
		return this.state.postType == 'notification' ? true : false
	}

	isOffer() {
		return ( this.state.postType == 'offer' || this.state.postType == 'events' ) ? true : false
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
			{ value: 'notification', label: 'Notification' },
			{ value: 'offer', label: 'Offer' },
			{ value: 'events', label: 'Events' },
		]
		
		let availableCategories = [];
		app.categories.map(category => {
			availableCategories.push({
				value: category.id,
				label: category.name
			})
		})

		let availableSubcategories = this.state.availableSubcategories.map(subcategory => {
			return {
				value: subcategory.id,
				label: subcategory.name
			}
		})

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
						<div className="form-group">
							<label htmlFor="source">Source</label>
							<Select
								name="source"
								value={this.state.source}
								options={availableSources}
								onChange={this.handleSourceChange} />
						</div>
					</div>
					<div className="col-md-8">
						{ this.isNotFromCityCard() ?
							<div className="row">
								<div className="col-md-6">
									<div className="form-group">
										<label htmlFor="source_from">From</label>
										<Select
											name="source_from"
											value={this.state.source_from}
											options={availableSourcesFrom}
											onChange={this.handleSourceFromChange} />
									</div>
								</div>
								<div className="col-md-6">
									<div className="form-group">
										<label htmlFor="source_link">Link</label>
										<input
											type="text"
											name="source_link"
											id="source_link"
											placeholder="eg. http://google.com"
											className="form-control"
											value={this.state.source_link}
											onChange={this.handleChange} />
									</div>
								</div>
							</div>
							: <div></div>
						}
					</div>
				</div>

				<div className="form-group">
					<label htmlFor="type">Post Type</label>
					<Select
						name="type"
						value={this.state.type}
						options={availablePostTypes}
						onChange={this.handleTypeChange} />
				</div>

				<div className="form-group">
					<label htmlFor="outlet_ids">Select Outlets</label>
					<Select
						name="outlet_ids"
						multi={true}
						value={this.state.outlet_ids}
						joinValues
						options={availableOutlets}
						onChange={this.handleSelectedOutletsChange} />
				</div>

				<div className="row">
					<div className="col-md-6">
						<div className="form-group">
							<label htmlFor="category">Category</label>
							<Select
								name="category"
								value={this.state.selectedCategory}
								options={availableCategories}
								onChange={this.handleCategoryChange} />
						</div>
					</div>
					<div className="col-md-6">
						<div className="form-group">
							<label htmlFor="category">Subcategories</label>
							<Select
								name="subcategories"
								isLoading={this.state.isFetchingSubcategories}
								value={this.state.selectedSubcategories}
								options={availableSubcategories}
								multi={true}
								joinValues
								onChange={this.handleSubcategoryChange} />
						</div>
					</div>
				</div>

				<div className="form-group">
					<label htmlFor="title">Title</label>
					<input type="text"
						name="title"
						id="title"
						value={this.state.title}
						onChange={this.handleChange}
						className="form-control" />
				</div>

				<div className="form-group">
					<label htmlFor="editor">Description</label>
					<textarea
						name="desc"
						id="editor"
						className="form-control"
						defaultValue={this.state.desc}
						onChange={this.handleChange}
					></textarea>
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
	<CreatePost />,
	document.getElementById('CreatePost')
);

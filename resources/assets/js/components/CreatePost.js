import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import Select from 'react-select';
import Categories from './Categories';

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
			title: '',
			desc: '',
			outlet_ids: [],

			errors: [],
		}

		this.handleChange = this.handleChange.bind(this);
		this.handleSourceChange = this.handleSourceChange.bind(this);
		this.handleSourceFromChange = this.handleSourceFromChange.bind(this);
		this.handleTypeChange = this.handleTypeChange.bind(this);
		this.handleSelectedOutletsChange = this.handleSelectedOutletsChange.bind(this);
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

	onSubmit(e) {
		e.preventDefault();
		this.isSubmitting();

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
		    	this.resetSubmitButton();
				let errors = error.responseJSON;
				this.setState({ errors });
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

	isNotFromCityCard() {
		return this.state.source == 'external' ? true : false
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
			{ value: '', label: '' },
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

				<Categories errors={errors} />

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

// EditPost.js
import React from 'react';
import ReactDOM from 'react-dom';
import Select from 'react-select';
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

			isExternal: app.post.isExternal,

			source: app.post.isExternal == true ? 'external' : 'citycard',
			source_from: app.post.isExternal == true ? app.post.sources[0].name : '',
			source_link: app.post.isExternal == true ? app.post.sources[0].pivot.link : '',

			currentOutlets: '',

			category: '',
			subcategories: [],

			currentCategory: '',

			errors: [],
		}

		this.onSubmit = this.onSubmit.bind(this);
		this.handleSourcesChange = this.handleSourcesChange.bind(this);
	}

	componentDidMount() {
		// Outlets
		let currentOutlets = [];
		app.post.outlets.map(outlet => {
			currentOutlets.push({
				value: outlet.id,
				label: outlet.name
			})
		})

		this.setState({ currentOutlets })

		// Category
		this.setState({ currentCategory: app.post.category.id })
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

		console.log('Submitting...');
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
									<div className="form-group">
										<label htmlFor="source_from" className="control-label">From</label>
										<Select name="source_from" 
											value={this.state.source_from} 
											options={availableSourcesFrom}
											onChange={(e) => this.setState({ source_from: e.value}) } />										
									</div>
								</div>
								<div className="col-md-6">
									<div className="form-group">
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

				<div className="form-group">
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
	            	currentCategory={this.state.currentCategory}
	            	handleCategoryChange={(category) => this.setState({ category })}
	            	handleSubcategoryChange={(subcategories) => this.setState({ subcategories })} />

				<div className="form-group">
					<label htmlFor="title" className="control-label">Title</label>
					<input type="text" name="title" id="title" className="form-control" value={this.state.title} onChange={() => console.log('hi')} />
				</div>

				<div className="form-group">
					<label htmlFor="editor" className="control-label">Description</label>
					<textarea name="desc" id="editor" className="form-control" defaultValue={this.state.desc} onChange={() => console.log('hi') }></textarea>				
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

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
			source: '',
			source_from: '',
			source_link: '',
			type: '',
			outlets: [],
			title: '',
			desc: '',
			outlet_ids: [],
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
		let source = e.target.value;
		this.setState({ source })
	}

	handleSourceFromChange(e) {
		let source_from = e.target.value;
		this.setState({
			source_from
		})
	}

	handleTypeChange(e) {
		let type = e.target.value;
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
		console.log('CreatePostForm' + $('#CreatePostForm').serialize());

		let merchant = app.merchant;
		let url = '/dashboard/merchants/' + merchant.id + '/posts/';

		$.ajax({
		    url: url,
		    type: 'POST',
		    data: $('#CreatePostForm').serialize(),
		    headers: { 'X-CSRF-Token': App.csrfToken },
		    success: function(response) {
				console.log(response);
		        swal({
		            title: "City Card",
		            text: "You have successfully created a post.",
		            type: "success",
		            showConfirmButton: true
		        });

		        this.setState({
		            submitButtonText: 'Save',
		            isSubmitted: false
		        })

		        window.location = '/dashboard/merchants/' + merchant.id + '/posts/' + response.id;
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

	componentDidMount() {

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
		return ( this.state.postType == 'offer' || this.state.postType == 'ticket' ) ? true : false
	}

	render() {
		let merchant = app.merchant;
		let outlets = app.outlets;
		let sources = app.sources;

		let sourcesFrom = sources.map(source => {
			return (
				<option value={source.id} key={source.id}>
					{source.name}
				</option>
			)
		})

		let availableOutlets = []
		outlets.map(outlet => {
			availableOutlets.push({
				value: outlet.id,
				label: outlet.name
			})
		})

		return (
			<form method="POST" id="CreatePostForm" onSubmit={this.onSubmit.bind(this)}>
				<div className="form-group">
					<label htmlFor="merchant">Merchant Name</label>
					<input
						type="text"
						name="merchant"
						id="merchant"
						className="form-control"
						value={merchant.name} disabled />
				</div>

				<div className="row">
					<div className="col-md-4">
						<div className="form-group">
							<label htmlFor="source">Source</label>
							<select
								name="source"
								id="source"
								className="form-control"
								defaultValue={this.state.source}
								onChange={this.handleSourceChange}
							>
								<option value=""></option>
								<option value="citycard">City Card</option>
								<option value="external">External</option>
							</select>
						</div>
					</div>
					<div className="col-md-8">
						{ this.isNotFromCityCard() ?
							<div className="row">
								<div className="col-md-6">
									<div className="form-group">
										<label htmlFor="source_from">From</label>
										<select
											name="source_from"
											id="source_from"
											className="form-control"
											defaultValue={this.state.source_from}
											onChange={this.handleSourceFromChange}
										>
											{sourcesFrom}
										</select>
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
					<select
						name="type"
						id="type"
						className="form-control"
						defaultValue={this.state.type}
						onChange={this.handleTypeChange}
					>
						<option value=""></option>
						<option value="notification">Notification</option>
						<option value="offer">Offer</option>
						<option value="ticket">Ticket</option>
					</select>
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

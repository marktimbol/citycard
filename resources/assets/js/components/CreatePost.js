import React, { Component } from 'react';
import ReactDOM from 'react-dom';

class CreatePost extends Component
{
	constructor(props) {
		super(props);

		this.state = {
			isSubmitted: false,
			submitButtonText: 'Save',
			source: '',
			postType: '',
		}

		this.handleChange = this.handleChange.bind(this);
		this.handleSourceChange = this.handleSourceChange.bind(this);
		this.handleTypeChange = this.handleTypeChange.bind(this);
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

	handleTypeChange(e) {
		let postType = e.target.value;
		this.setState({ postType })
	}

	onSubmit(e) {
		e.preventDefault();
		this.isSubmitting();

		let merchant = app.merchant;
		let url = '/dashboard/merchants/' + merchant.id + '/posts/';
		$.ajax({
		    url: url,
		    type: 'POST',
		    data: $('#CreatePost').serialize(),
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

		let availableOutlets = outlets.map(outlet => {
			return (
				<option value={outlet.id} key={outlet.id}>
					{outlet.name}
				</option>
			)
		})

		return (
			<form method="POST" id="CreatePost" onSubmit={this.onSubmit.bind(this)}>
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
										>
											<option value="Cobone">Cobone</option>
											<option value="Groupon">Groupon</option>
										</select>
									</div>
								</div>
								<div className="col-md-6">
									<div className="form-group">
										<label htmlFor="link">Link</label>
										<input type="text"
											name="link"
											id="link"
											value={this.state.link}
											placeholder="eg. http://google.com"
											className="form-control" />
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
					<select
						name="outlet_ids[]"
						id="outlet_ids"
						className="form-control select2"
						multiple
					>
						<option value=""></option>
						{availableOutlets}
					</select>
				</div>

				<div className="form-group">
					<label htmlFor="title">Title</label>
					<input type="text"
						name="title"
						id="title"
						value={this.state.title}
						className="form-control" />
				</div>

				{ this.isFromCityCard() ?
					this.isOffer() ?
						<div className="row">
							<div className="col-md-4">
								<div className="form-group">
									<label htmlFor="price">Price</label>
									<div className="input-group">
										<span className="input-group-addon">AED</span>
										<input type="text"
										name="price"
										id="price"
										value={this.state.price}
										className="form-control" />
									</div>
								</div>
							</div>
						</div>
						: <div></div>
					:
					<div></div>
				}

				<div className="form-group">
					<label htmlFor="editor">Description</label>
					<textarea name="desc" id="editor" className="form-control">
						{this.state.desc}
					</textarea>
				</div>

				{ this.isFromCityCard() ?
					this.isOffer() ?
						<div>
							<h3>Payment Option</h3>
							<div className="form-group">
								<label htmlFor="type">The customer can pay using</label>
								<div className="radio">
									<label>
										<input type="radio" name="payment_option" value="both" /> Cashback &amp; Points
									</label>
								</div>
								<div className="radio">
									<label>
										<input type="radio" name="payment_option" value="cashback" /> Cashback
									</label>
								</div>
								<div className="radio">
									<label>
										<input type="radio" name="payment_option" value="points" /> Points
									</label>
								</div>
							</div>
							<div className="row">
								<div className="col-md-5">
									<div className="form-group">
										<label htmlFor="points">How many points the customer will earn when they purchased this offer?</label>
										<input type="text"
										name="points"
										id="points"
										value={this.state.points}
										className="form-control" />
									</div>
								</div>
							</div>
						</div>
						: <div></div>
					: <div></div>
				}
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
